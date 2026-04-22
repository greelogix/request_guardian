<?php

declare(strict_types=1);

namespace YourVendor\LaravelAutoValidator;

use Illuminate\Http\Request;
use YourVendor\LaravelAutoValidator\Resolvers\FieldTypeResolver;

class RuleGenerator
{
    public function __construct(private readonly FieldTypeResolver $resolver)
    {
    }

    public function generateRules(Request $request): array
    {
        $rules = [];
        $excluded = config('auto-validator.exclude_fields', []);

        foreach ($request->allFiles() + $request->all() as $field => $value) {
            if (in_array($field, $excluded, true)) {
                continue;
            }

            $type = $this->resolver->resolve((string) $field, $value);
            $rules[$field] = $this->resolver->rulesForType($type, (string) $field, $value);
        }

        return $rules;
    }
}
