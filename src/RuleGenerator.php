<?php

declare(strict_types=1);

namespace Greelogix\RequestGuardian;

use Illuminate\Http\Request;
use Greelogix\RequestGuardian\Contracts\RequestRuleGenerator as RequestRuleGeneratorContract;

/**
 * @deprecated Use \Greelogix\RequestGuardian\Contracts\RequestRuleGenerator instead.
 */
class RuleGenerator
{
    public function __construct(private readonly RequestRuleGeneratorContract $generator)
    {
    }

    public function generateRules(Request $request): array
    {
        return $this->generator->generate($request);
    }
}
