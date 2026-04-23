<?php

declare(strict_types=1);

namespace Greelogix\RequestGuardian\Services;

use Illuminate\Http\Request;
use Greelogix\RequestGuardian\Contracts\RequestRuleGenerator as RequestRuleGeneratorContract;
use Greelogix\RequestGuardian\Resolvers\FieldTypeResolver;
use Greelogix\RequestGuardian\Support\SkipValidationPolicy;

class RequestRuleGenerator implements RequestRuleGeneratorContract
{
    public function __construct(
        private readonly FieldTypeResolver $resolver,
        private readonly SkipValidationPolicy $skipPolicy
    ) {
    }

    public function generate(Request $request): array
    {
        if ($this->skipPolicy->shouldSkipRequest($request)) {
            return [];
        }

        $rules = [];
        $excluded = (array) config('auto-validator.exclude_fields', []);

        foreach ($request->allFiles() + $request->all() as $field => $value) {
            $field = (string) $field;

            if (in_array($field, $excluded, true) || $this->skipPolicy->shouldSkipField($field)) {
                continue;
            }

            $type = $this->resolver->resolve($field, $value);
            if ($this->skipPolicy->shouldSkipType($type)) {
                continue;
            }

            $fieldRules = $this->resolver->rulesForType($type, $field, $value);
            $rules[$field] = $this->skipPolicy->filterRules($request, $field, $type, $fieldRules);
        }

        return $rules;
    }
}
