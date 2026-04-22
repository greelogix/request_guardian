<?php

declare(strict_types=1);

namespace YourVendor\LaravelAutoValidator\Validators;

class ArrayValidator extends BaseValidator
{
    public function supports(string $type): bool
    {
        return $type === 'array';
    }

    public function rules(string $field, mixed $value = null): array
    {
        return ['array'];
    }
}
