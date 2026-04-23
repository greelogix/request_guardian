<?php

declare(strict_types=1);

namespace Greelogix\RequestGuardian\Validators;

class CustomValidator extends BaseValidator
{
    public function supports(string $type): bool
    {
        return $type === 'custom';
    }

    public function rules(string $field, mixed $value = null): array
    {
        return [];
    }
}
