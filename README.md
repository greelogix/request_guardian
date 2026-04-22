# Request Guardian

`greelogix/request_guardian` is a Laravel package that adds automatic request validation with smart field type detection.

## Features

- Zero-config validation for common request payload fields
- Trait-based integration for Form Requests and manual controller usage
- Config-driven patterns for email, phone, URL, slug, UUID, IP, and more
- File validation presets for images, documents, videos, and audio
- Input sanitization (trim, strip tags, normalize spaces, etc.)
- Validation lifecycle events

## Installation

You can install this package in two ways.

### Option A: Add repository in `composer.json` (recommended for private or GitHub-only install)

```json
{
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/greelogix/request_guardian"
    }
  ]
}
```

Then run:

```bash
composer require greelogix/request_guardian:dev-main
```

### Option B: Install later via `composer require`

If the package is available directly to Composer (for example via Packagist), run:

```bash
composer require greelogix/request_guardian
```

After install, publish config:

```bash
php artisan vendor:publish --provider="YourVendor\\LaravelAutoValidator\\LaravelAutoValidatorServiceProvider" --tag=auto-validator-config
```

## Quick Start

### Form Request

```php
use Illuminate\Foundation\Http\FormRequest;
use YourVendor\LaravelAutoValidator\Traits\AutoValidatesTrait;

class StoreUserRequest extends FormRequest
{
    use AutoValidatesTrait;

    public function authorize(): bool
    {
        return true;
    }
}
```

### Controller

```php
use Illuminate\Http\Request;
use YourVendor\LaravelAutoValidator\Traits\AutoValidatesTrait;

class UserController
{
    use AutoValidatesTrait;

    public function store(Request $request)
    {
        $validated = $this->autoValidate($request)->validated();

        return response()->json($validated);
    }
}
```

See `docs/USAGE.md` and `docs/CONFIGURATION.md` for full details.
