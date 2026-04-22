<?php

declare(strict_types=1);

namespace YourVendor\LaravelAutoValidator\Validators;

class NumericValidator extends BaseValidator
{
    public function supports(string $type): bool
    {
        return in_array($type, ['integer', 'decimal', 'percentage', 'currency'], true);
    }

    public function rules(string $field, mixed $value = null): array
    {
        if (preg_match('/percent|rate/i', $field)) {
            return ['numeric', 'between:0,100'];
        }

        if (preg_match('/amount|price|cost|total/i', $field)) {
            return ['numeric', 'min:0'];
        }

        return is_int($value) ? ['integer'] : ['numeric'];
    }
}
