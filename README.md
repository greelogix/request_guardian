# Request Guardian

`greelogix/request_guardian` is a Laravel package that adds automatic request validation with smart field type detection.

## Features

- Zero-config validation for common request payload fields
- Automatic request validation via middleware (no trait required)
- Optional trait integration for Form Requests/manual controller flows
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
php artisan vendor:publish --provider="Greelogix\\RequestGuardian\\RequestGuardianServiceProvider" --tag=auto-validator-config
```

## Quick Start

### Automatic (No Trait Needed)

Once installed, validation runs automatically for write requests when `auto_middleware` is enabled (enabled by default).

By default, the middleware policy is:
- validates only `POST|PUT|PATCH|DELETE`
- skips common framework/tooling route names and paths (`ignition`, `livewire`, `horizon`, `telescope`, `sanctum`)
- allows explicit route/path exclusions via `skip_validation.routes`

```php
public function store(\Illuminate\Http\Request $request)
{
    // Available through Request macro:
    $validated = $request->validated();
    return response()->json($validated);
}
```

### Optional Form Request Trait

```php
use Illuminate\Foundation\Http\FormRequest;
use Greelogix\RequestGuardian\Traits\AutoValidatesTrait;

class StoreUserRequest extends FormRequest
{
    use AutoValidatesTrait;

    public function authorize(): bool
    {
        return true;
    }
}
```

### Optional Controller Trait

```php
use Illuminate\Http\Request;
use Greelogix\RequestGuardian\Traits\AutoValidatesTrait;

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

## Skip Validation Configuration (JSON example)

Use these keys in `config/auto-validator.php` (`skip_validation` section):

```json
{
  "skip_validation": {
    "routes": ["webhook/*"],
    "fields": ["remember"],
    "types": [],
    "rules": [],
    "field_rules": {
      "password": ["strong_password"]
    },
    "type_rules": {
      "password": ["strong_password"]
    },
    "route_rules": {
      "login": ["strong_password"]
    }
  }
}
```

This keeps auto-validation enabled, but lets you skip specific rules by field/type/route.

See `docs/USAGE.md` and `docs/CONFIGURATION.md` for full details.
