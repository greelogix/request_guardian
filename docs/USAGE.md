# Usage

- Use `AutoValidatesTrait` in a `FormRequest` for automatic sanitization and validation.
- Use `autoValidate(Request $request)` in controllers/services for manual execution.
- Add optional `customValidationRules()` to append project-specific rules.
- Add optional `messages()` to override default error messages.
