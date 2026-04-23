<?php

declare(strict_types=1);

namespace Greelogix\RequestGuardian;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Factory as ValidationFactory;
use Greelogix\RequestGuardian\Contracts\RequestRuleGenerator as RequestRuleGeneratorContract;
use Greelogix\RequestGuardian\Http\Middleware\ValidateRequest;
use Greelogix\RequestGuardian\Resolvers\FieldTypeResolver;
use Greelogix\RequestGuardian\Services\RequestRuleGenerator;
use Greelogix\RequestGuardian\Support\SkipValidationPolicy;
use Greelogix\RequestGuardian\Validators\ArrayValidator;
use Greelogix\RequestGuardian\Validators\CustomValidator;
use Greelogix\RequestGuardian\Validators\DateValidator;
use Greelogix\RequestGuardian\Validators\FileValidator;
use Greelogix\RequestGuardian\Validators\NumericValidator;
use Greelogix\RequestGuardian\Validators\StringValidator;
use Illuminate\Support\Facades\Validator;

class LaravelAutoValidatorServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/Resources/config/auto-validator.php', 'auto-validator');

        $this->app->singleton(SkipValidationPolicy::class);
        $this->app->singleton(FieldTypeResolver::class, fn () => new FieldTypeResolver(config('auto-validator', [])));
        $this->app->singleton(RequestRuleGeneratorContract::class, RequestRuleGenerator::class);
        $this->app->singleton(RequestRuleGenerator::class, fn ($app) => $app->make(RequestRuleGeneratorContract::class));

        // Backward compatibility for previous public API.
        $this->app->singleton(RuleGenerator::class, RuleGenerator::class);

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
            $rules = app(RequestRuleGeneratorContract::class)->generate($this);
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
