<?php

declare(strict_types=1);

namespace Greelogix\RequestGuardian\Contracts;

use Illuminate\Http\Request;

interface RequestRuleGenerator
{
    /**
     * Build Laravel validation rules for the given request payload.
     *
     * @return array<string, array<int, string>>
     */
    public function generate(Request $request): array;
}
