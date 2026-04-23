<?php

declare(strict_types=1);

namespace Greelogix\RequestGuardian\Validators;

abstract class BaseValidator
{
    public function __construct(protected readonly array $config)
    {
    }

    abstract public function supports(string $type): bool;

    abstract public function rules(string $field, mixed $value = null): array;
}
