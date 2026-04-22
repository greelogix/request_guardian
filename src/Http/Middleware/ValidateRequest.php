<?php

declare(strict_types=1);

namespace YourVendor\LaravelAutoValidator\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use YourVendor\LaravelAutoValidator\Resolvers\FieldTypeResolver;

class ValidateRequest
{
    public function __construct(private readonly FieldTypeResolver $resolver)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        if (config('auto-validator.enabled', true) && !$request->isMethodSafe(false)) {
            $rules = [];
            foreach ($request->allFiles() + $request->except(config('auto-validator.exclude_fields', [])) as $field => $value) {
                $type = $this->resolver->resolve((string) $field, $value);
                $rules[$field] = $this->resolver->rulesForType($type, (string) $field, $value);
            }

            if ($rules !== []) {
                $validated = $request->validate($rules, config('auto-validator.error_messages', []));
                $request->attributes->set('_auto_validated', $validated);
            }
        }

        return $next($request);
    }
}
