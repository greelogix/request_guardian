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
        if ($this->shouldSkipAllValidationForRequest($request)) {
            return [];
        }

        foreach ($request->allFiles() + $request->all() as $field => $value) {
            if (in_array($field, $excluded, true)) {
                continue;
            }
            if ($this->shouldSkipField((string) $field)) {
                continue;
            }

            $type = $this->resolver->resolve((string) $field, $value);
            if ($this->shouldSkipType($type)) {
                continue;
            }
            $rules[$field] = $this->resolver->rulesForType(
                $type,
                (string) $field,
                $value
            );
            $rules[$field] = $this->filterSkippedRules($request, (string) $field, $type, $rules[$field]);
        }

        return $rules;
    }

    private function shouldSkipAllValidationForRequest(Request $request): bool
    {
        $routes = (array) config('auto-validator.skip_validation.routes', []);

        foreach ($routes as $routePattern) {
            if ($request->routeIs($routePattern) || $request->is($routePattern)) {
                return true;
            }
        }

        return false;
    }

    private function shouldSkipField(string $field): bool
    {
        return in_array($field, (array) config('auto-validator.skip_validation.fields', []), true);
    }

    private function shouldSkipType(string $type): bool
    {
        return in_array($type, (array) config('auto-validator.skip_validation.types', []), true);
    }

    private function filterSkippedRules(Request $request, string $field, string $type, array $rules): array
    {
        $skipRules = (array) config('auto-validator.skip_validation.rules', []);
        $fieldRuleSkips = (array) config('auto-validator.skip_validation.field_rules', []);
        $typeRuleSkips = (array) config('auto-validator.skip_validation.type_rules', []);
        $routeRuleSkips = (array) config('auto-validator.skip_validation.route_rules', []);

        $skipRules = array_merge(
            $skipRules,
            (array) ($fieldRuleSkips[$field] ?? []),
            (array) ($typeRuleSkips[$type] ?? []),
            $this->routeRuleSkipsForRequest($request, $routeRuleSkips)
        );

        if ($skipRules === []) {
            return $rules;
        }

        $normalizedSkip = array_map(
            fn (mixed $rule): string => $this->normalizeRuleName((string) $rule),
            $skipRules
        );

        return array_values(array_filter(
            $rules,
            fn (mixed $rule): bool => !in_array($this->normalizeRuleName((string) $rule), $normalizedSkip, true)
        ));
    }

    private function routeRuleSkipsForRequest(Request $request, array $routeRuleSkips): array
    {
        $rules = [];

        foreach ($routeRuleSkips as $routePattern => $skipList) {
            if ($request->routeIs((string) $routePattern) || $request->is((string) $routePattern)) {
                $rules = array_merge($rules, (array) $skipList);
            }
        }

        return $rules;
    }

    private function normalizeRuleName(string $rule): string
    {
        $parts = explode(':', $rule, 2);

        return trim($parts[0]);
    }
}
