<?php

declare(strict_types=1);

namespace Greelogix\RequestGuardian\Validators;

class DateValidator extends BaseValidator
{
    public function supports(string $type): bool
    {
        return in_array($type, ['date', 'time', 'datetime', 'timestamp'], true);
    }

    public function rules(string $field, mixed $value = null): array
    {
        if (str_contains($field, 'time')) {
            return ['date_format:H:i'];
        }

        if (str_contains($field, 'timestamp')) {
            return ['integer'];
        }

        return ['date'];
    }
}
