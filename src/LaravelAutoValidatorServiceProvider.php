<?php

declare(strict_types=1);

namespace YourVendor\LaravelAutoValidator;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Factory as ValidationFactory;
use YourVendor\LaravelAutoValidator\Http\Middleware\ValidateRequest;
use YourVendor\LaravelAutoValidator\Resolvers\FieldTypeResolver;
use YourVendor\LaravelAutoValidator\Validators\ArrayValidator;
use YourVendor\LaravelAutoValidator\Validators\CustomValidator;
use YourVendor\LaravelAutoValidator\Validators\DateValidator;
use YourVendor\LaravelAutoValidator\Validators\FileValidator;
use YourVendor\LaravelAutoValidator\Validators\NumericValidator;
use YourVendor\LaravelAutoValidator\Validators\StringValidator;
use Illuminate\Support\Facades\Validator;

class LaravelAutoValidatorServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/Resources/config/auto-validator.php', 'auto-validator');

        $this->app->singleton(FieldTypeResolver::class, fn () => new FieldTypeResolver(config('auto-validator', [])));
        $this->app->singleton(StringValidator::class, fn () => new StringValidator(config('auto-validator', [])));
        $this->app->singleton(NumericValidator::class, fn () => new NumericValidator(config('auto-validator', [])));
        $this->app->singleton(DateValidator::class, fn () => new DateValidator(config('auto-validator', [])));
        $this->app->singleton(FileValidator::class, fn () => new FileValidator(config('auto-validator', [])));
        $this->app->singleton(ArrayValidator::class, fn () => new ArrayValidator(config('auto-validator', [])));
        $this->app->singleton(CustomValidator::class, fn () => new CustomValidator(config('auto-validator', [])));
    }

    public function boot(Kernel $kernel, ValidationFactory $validator): void
    {
        $this->publishes([
            __DIR__ . '/Resources/config/auto-validator.php' => config_path('auto-validator.php'),
        ], 'auto-validator-config');

        if (config('auto-validator.enabled', true) && config('auto-validator.auto_middleware', false)) {
            $kernel->pushMiddleware(ValidateRequest::class);
        }

        Request::macro('autoValidate', function () {
            /** @var Request $this */
            $rules = app(RuleGenerator::class)->generateRules($this);
            $validator = Validator::make($this->all(), $rules, (array) config('auto-validator.error_messages', []));
            $validated = $validator->validate();
            $this->attributes->set('_auto_validated', $validated);

            return $validator;
        });

        Request::macro('validated', function (?string $key = null, mixed $default = null): mixed {
            /** @var Request $this */
            $data = (array) $this->attributes->get('_auto_validated', []);
            if ($key === null) {
                return $data;
            }

            return data_get($data, $key, $default);
        });

        $validator->extend('strong_password', function (string $attribute, mixed $value): bool {
            if (!is_string($value)) {
                return false;
            }

            $settings = config('auto-validator.password_strength', []);
            $minLength = (int) ($settings['min_length'] ?? 8);

            if (mb_strlen($value) < $minLength) {
                return false;
            }

            if (($settings['require_uppercase'] ?? true) && !preg_match('/[A-Z]/', $value)) {
                return false;
            }

            if (($settings['require_lowercase'] ?? true) && !preg_match('/[a-z]/', $value)) {
                return false;
            }

            if (($settings['require_numbers'] ?? true) && !preg_match('/\d/', $value)) {
                return false;
            }

            $specialPattern = (string) ($settings['special_chars_pattern'] ?? '/[!@#$%^&*()_+\-=\[\]{};:\'\",.<>?\/\\|`~]/');
            if (($settings['require_special_chars'] ?? true) && !preg_match($specialPattern, $value)) {
                return false;
            }

            return true;
        }, (string) config('auto-validator.error_messages.password_strength', 'The :attribute is too weak.'));
    }
}
