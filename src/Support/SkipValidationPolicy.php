<?php

declare(strict_types=1);

namespace Greelogix\RequestGuardian\Support;

use Illuminate\Http\Request;

class SkipValidationPolicy
{
    public function shouldSkipRequest(Request $request): bool
    {
        $routes = (array) config('auto-validator.skip_validation.routes', []);

        foreach ($routes as $routePattern) {
            if ($request->routeIs((string) $routePattern) || $request->is((string) $routePattern)) {
                return true;
            }
        }

        return false;
    }

    public function shouldSkipField(string $field): bool
    {
        return in_array($field, (array) config('auto-validator.skip_validation.fields', []), true);
    }

    public function shouldSkipType(string $type): bool
    {
        return in_array($type, (array) config('auto-validator.skip_validation.types', []), true);
    }

    /**
     * @param  array<int, string>  $rules
     * @return array<int, string>
     */
    public function filterRules(Request $request, string $field, string $type, array $rules): array
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

    /**
     * @param  array<string, mixed>  $routeRuleSkips
     * @return array<int, string>
     */
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
