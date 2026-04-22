# API Reference

## Trait

- `autoValidate(Request $request): \Illuminate\Validation\Validator`
- `getValidationRules(Request $request, ?array $payload = null): array`
- `validationMessages(): array`
- `validated($key = null, $default = null): mixed`

## Resolver

- `resolve(string $field, mixed $value): string`
- `rulesForType(string $type, string $field, mixed $value): array`
