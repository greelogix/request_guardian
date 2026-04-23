<?php

declare(strict_types=1);

namespace Greelogix\RequestGuardian\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Greelogix\RequestGuardian\Contracts\RequestRuleGenerator;

class ValidateRequest
{
    public function __construct(private readonly RequestRuleGenerator $ruleGenerator)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        if (config('auto-validator.enabled', true) && !$request->isMethodSafe(false)) {
            $rules = $this->ruleGenerator->generate($request);

            if ($rules !== []) {
                $validated = $request->validate($rules, config('auto-validator.error_messages', []));
                $request->attributes->set('_auto_validated', $validated);
            }
        }

        return $next($request);
    }
}
