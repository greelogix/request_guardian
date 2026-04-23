<?php

declare(strict_types=1);

namespace Greelogix\RequestGuardian\Validators;

class StringValidator extends BaseValidator
{
    public function supports(string $type): bool
    {
        return in_array($type, ['string', 'email', 'phone', 'url', 'username', 'slug', 'password', 'ip', 'uuid', 'domain', 'credit_card', 'mac_address', 'json'], true);
    }

    public function rules(string $field, mixed $value = null): array
    {
        return ['string'];
    }

    public function looksRequired(string $field): bool
    {
        return (bool) preg_match('/name|email|phone|password|title|slug|username/i', $field);
    }
}
