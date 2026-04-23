<?php

declare(strict_types=1);

namespace Greelogix\RequestGuardian\Traits;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Validator as LaravelValidator;
use Greelogix\RequestGuardian\Resolvers\FieldTypeResolver;
use Greelogix\RequestGuardian\Validators\StringValidator;

trait AutoValidatesTrait
{
    protected array $autoValidatedData = [];

    public function autoValidate(Request $request): LaravelValidator
    {
        $payload = $this->sanitizeInput($request->all());
        $rules = $this->getValidationRules($request, $payload);

        Event::dispatch('auto-validator.validating', [$request, $rules]);

        $validator = Validator::make($payload, $rules, $this->validationMessages());

        if ($validator->fails()) {
            Event::dispatch('auto-validator.failed', [$request, $validator->errors()->toArray()]);
        } else {
            Event::dispatch('auto-validator.validated', [$request, $validator->validated()]);
        }

        return $validator;
    }

    public function getValidationRules(Request $request, ?array $payload = null): array
    {
        if (!config('auto-validator.enabled', true)) {
            return method_exists($this, 'rules') ? $this->rules() : [];
        }

        $resolver = app(FieldTypeResolver::class);
        $stringValidator = app(StringValidator::class);
        $payload = $payload ?? $request->all();

        $rules = [];
        $exclude = config('auto-validator.exclude_fields', []);
        $strictMode = (bool) config('auto-validator.strict_mode', false);
        $allowedFields = (array) config('auto-validator.allowed_fields', []);

        foreach ($payload as $field => $value) {
            if (in_array($field, $exclude, true)) {
                continue;
            }

            if ($strictMode && $allowedFields !== [] && !in_array($field, $allowedFields, true)) {
                $rules[$field] = ['prohibited'];
                continue;
            }

            $type = $resolver->resolve($field, $value);
            $fieldRules = $resolver->rulesForType($type, $field, $value);

            $fieldOverrides = (array) config('auto-validator.field_rules', []);
            if (isset($fieldOverrides[$field]) && is_array($fieldOverrides[$field])) {
                $fieldRules = array_merge($fieldRules, $fieldOverrides[$field]);
            }

            if (is_string($value) && $stringValidator->looksRequired($field)) {
                array_unshift($fieldRules, 'required');
            } else {
                array_unshift($fieldRules, 'nullable');
            }

            $rules[$field] = array_values(array_unique(array_filter($fieldRules)));
        }

        if (method_exists($this, 'customValidationRules')) {
            /** @var array<string, array|string> $customRules */
            $customRules = $this->customValidationRules();
            $rules = array_merge($rules, $customRules);
        }

        return $rules;
    }

    public function validationMessages(): array
    {
        $messages = config('auto-validator.error_messages', []);

        if (method_exists($this, 'messages')) {
            $messages = array_merge($messages, $this->messages());
        }

        return $messages;
    }

    protected function sanitizeInput(array $input): array
    {
        $sanitize = config('auto-validator.sanitize', true);

        if ($sanitize === false) {
            return $input;
        }

        $options = is_array($sanitize)
            ? $sanitize
            : [
                'trim' => true,
                'strip_tags' => true,
                'normalize_spaces' => true,
                'lowercase_email' => true,
                'format_phone' => true,
                'remove_currency_symbols' => true,
            ];

        array_walk_recursive($input, function (&$value, $key) use ($options): void {
            if (!is_string($value)) {
                return;
            }

            if ($options['trim'] ?? true) {
                $value = trim($value);
            }

            if ($options['strip_tags'] ?? true) {
                $value = strip_tags($value);
            }

            if ($options['normalize_spaces'] ?? true) {
                $value = preg_replace('/\s+/', ' ', $value) ?? $value;
            }

            if (($options['lowercase_email'] ?? true) && str_contains((string) $key, 'email')) {
                $value = mb_strtolower($value);
            }

            if (($options['format_phone'] ?? true) && str_contains((string) $key, 'phone')) {
                $value = preg_replace('/[^\d\+]/', '', $value) ?? $value;
            }

            if (($options['remove_currency_symbols'] ?? true) && preg_match('/amount|price|cost|total/i', (string) $key)) {
                $value = preg_replace('/[^\d.,\-]/', '', $value) ?? $value;
            }

            if ($options['remove_invisible_unicode'] ?? true) {
                $value = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $value) ?? $value;
            }
        });

        return $input;
    }

    protected function prepareForValidation(): void
    {
        if ($this instanceof FormRequest) {
            $this->merge($this->sanitizeInput($this->all()));
        }
    }

    public function passedValidation(): void
    {
        if ($this instanceof FormRequest) {
            $validator = $this->autoValidate($this);
            $validated = $validator->validate();
            $this->autoValidatedData = $validated;
            $this->replace(array_merge($this->all(), $validated));
        }
    }

    public function validated($key = null, $default = null): mixed
    {
        if (!empty($this->autoValidatedData)) {
            return Arr::get($this->autoValidatedData, $key, $default);
        }

        if (is_callable([parent::class, 'validated'])) {
            return parent::validated($key, $default);
        }

        return $default;
    }
}
