<?php

declare(strict_types=1);

return [
    'enabled' => env('AUTO_VALIDATOR_ENABLED', true),
    'auto_middleware' => env('AUTO_VALIDATOR_MIDDLEWARE', true),
    'strict_mode' => env('AUTO_VALIDATOR_STRICT', false),
    'allowed_fields' => [],
    'field_rules' => [],
    'field_types' => [],
    'sanitize' => [
        'trim' => true,
        'strip_tags' => true,
        'normalize_spaces' => true,
        'lowercase_email' => true,
        'format_phone' => true,
        'remove_currency_symbols' => true,
        'remove_invisible_unicode' => true,
        'normalize_unicode' => true,
    ],
    'string_limits' => [
        'default_max' => 65535,
        'email_max' => 254,
        'username_min' => 3,
        'username_max' => 32,
        'slug_max' => 150,
        'name_max' => 120,
        'password_max' => 255,
    ],
    'numeric_limits' => [
        'integer_min' => -2147483648,
        'integer_max' => 2147483647,
        'currency_min' => 0,
        'currency_max' => 999999999.99,
    ],
    'array_limits' => [
        'max_items' => 2000,
    ],
    'patterns' => [
        'email' => env('VALIDATOR_EMAIL_REGEX', '/^[^\s@]+@[^\s@]+\.[^\s@]+$/'),
        'phone' => env('VALIDATOR_PHONE_REGEX', '/^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/'),
        'url' => env('VALIDATOR_URL_REGEX', '/^https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b([-a-zA-Z0-9()@:%_\+.~#?&//=]*)$/'),
        'name' => '/^[\pL\s\-\'\.]+$/u',
        'username' => '/^[a-zA-Z0-9_]{3,20}$/',
        'slug' => '/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
        'ipv4' => '/^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/',
        'ipv6' => '/^(([0-9a-fA-F]{1,4}:){7}[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,7}:|([0-9a-fA-F]{1,4}:){1,6}:[0-9a-fA-F]{1,4})$/',
        'uuid' => '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i',
        'credit_card' => '/^(?:4[0-9]{12}(?:[0-9]{3})?|5[1-5][0-9]{14}|3[47][0-9]{13})$/',
        'domain' => '/^(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z]{2,}$/',
        'mac_address' => '/^(?:[0-9a-fA-F]{2}:){5}[0-9a-fA-F]{2}$/',
        'json' => '/^\{[\s\S]*\}$|^\[[\s\S]*\]$/',
        'locale' => '/^[a-z]{2}([_-][A-Z]{2})?$/',
        'hex_color' => '/^#(?:[0-9a-fA-F]{3}){1,2}$/',
        'csv' => '/^[^,\n]+(,[^,\n]+)*$/',
        'postal_code' => '/^[A-Za-z0-9\-\s]{3,12}$/',
    ],
    'detection_keywords' => [
        'email' => ['email', 'mail'],
        'phone' => ['phone', 'mobile', 'cell', 'contact_number'],
        'url' => ['url', 'website', 'link'],
        'ip' => ['ip', 'ipv4', 'ipv6'],
        'password' => ['password', 'pwd', 'pass'],
        'date' => ['date', 'dob', 'birthdate'],
        'time' => ['time'],
        'datetime' => ['datetime', 'published_at', 'scheduled_at'],
        'timestamp' => ['timestamp'],
        'uuid' => ['uuid'],
        'ulid' => ['ulid'],
        'slug' => ['slug', 'permalink'],
        'username' => ['username', 'handle'],
        'name' => ['name', 'fullname', 'first_name', 'last_name'],
        'domain' => ['domain', 'host'],
        'mac_address' => ['mac', 'mac_address'],
        'credit_card' => ['credit_card', 'card_number', 'cc_number'],
        'image' => ['image', 'avatar', 'photo', 'picture'],
        'document' => ['document', 'resume', 'cv'],
        'video' => ['video'],
        'audio' => ['audio', 'voice'],
        'file' => ['file', 'upload', 'attachment'],
        'integer' => ['age', 'count', 'qty', 'quantity', 'number'],
        'decimal' => ['decimal', 'float', 'ratio'],
        'currency' => ['amount', 'price', 'cost', 'total'],
        'percentage' => ['percent', 'percentage', 'rate'],
        'boolean' => ['is_', 'has_', 'can_', 'should_', 'active', 'enabled'],
        'json' => ['json', 'payload', 'meta'],
        'timezone' => ['timezone', 'tz'],
        'locale' => ['locale', 'lang', 'language'],
        'color' => ['color', 'hex_color'],
        'csv' => ['csv', 'list'],
        'postal_code' => ['postal_code', 'zip', 'zipcode'],
        'enum' => ['status', 'type', 'role'],
    ],
    'enum_fields' => [
        // 'status' => ['draft', 'published', 'archived'],
    ],
    'password_strength' => [
        'min_length' => 8,
        'require_uppercase' => true,
        'require_lowercase' => true,
        'require_numbers' => true,
        'require_special_chars' => true,
        'special_chars_pattern' => '/[!@#$%^&*()_+\-=\[\]{};:\'\",.<>?\/\\|`~]/',
    ],
    'skip_validation' => [
        // Validate only these HTTP methods globally.
        'methods' => ['POST', 'PUT', 'PATCH', 'DELETE'],
        // Example:
        // 'methods' => ['POST'],

        // Skip validation by Laravel route names (supports wildcards).
        'route_names' => ['ignition.*', 'horizon.*', 'telescope.*', 'livewire.*', 'sanctum.*'],
        // Example:
        // 'route_names' => ['admin.health.*'],

        // Skip validation by URL path patterns (supports wildcards).
        'paths' => ['_ignition/*', 'horizon/*', 'telescope/*', 'livewire/*', 'sanctum/*'],
        // Example:
        // 'paths' => ['internal/*'],

        // Skip all auto validation completely for these routes (name or path patterns).
        'routes' => [
            // 'webhook/*',
        ],
        // Example:
        // 'routes' => ['webhook/*', 'status/ping', 'api.internal.*'],

        // Skip selected fields globally.
        'fields' => [],
        // Example:
        // 'fields' => ['remember', 'captcha_token'],

        // Skip all rules for detected types.
        'types' => [],
        // Example:
        // 'types' => ['password', 'json'],

        // Skip these rules globally.
        'rules' => [],
        // Example:
        // 'rules' => ['strong_password', 'regex'],

        // Skip rules only for specific fields.
        'field_rules' => [],
        // Example:
        // 'field_rules' => [
        //     'password' => ['strong_password'],
        //     'username' => ['regex'],
        // ],

        // Skip rules only for specific detected types.
        'type_rules' => [],
        // Example:
        // 'type_rules' => [
        //     'email' => ['regex'],
        // ],

        // Skip rules only on matching routes.
        'route_rules' => [
            'login' => ['strong_password'],
        ],
        // Example:
        // 'route_rules' => [
        //     'login' => ['strong_password'],
        //     'auth/login' => ['strong_password'],
        //     'api/auth/*' => ['regex'],
        // ],
    ],
    // Define rules for your own custom detected type names.
    // Works with:
    // 1) detection_keywords (automatic by field name), or
    // 2) field_types (explicit field => type mapping).
    // JSON example:
    // {
    //   "field_types": { "sku_code": "sku" },
    //   "custom_type_rules": {
    //     "sku": ["string", "regex:/^[A-Z0-9-]{6,20}$/", "max:20"]
    //   }
    // }
    'custom_type_rules' => [],
    'file_uploads' => [
        'images' => [
            'extensions' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'],
            'mime_types' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'],
            'max_size' => 5120,
            'validate_dimensions' => true,
            'min_width' => 0,
            'min_height' => 0,
            'max_width' => 10000,
            'max_height' => 10000,
        ],
        'documents' => [
            'extensions' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt'],
            'mime_types' => ['application/pdf', 'application/msword', 'application/vnd.ms-excel'],
            'max_size' => 10240,
        ],
        'videos' => [
            'extensions' => ['mp4', 'avi', 'mov', 'mkv', 'webm'],
            'mime_types' => ['video/mp4', 'video/x-msvideo', 'video/quicktime'],
            'max_size' => 102400,
        ],
        'audio' => [
            'extensions' => ['mp3', 'wav', 'aac', 'flac'],
            'mime_types' => ['audio/mpeg', 'audio/wav', 'audio/aac'],
            'max_size' => 20480,
        ],
    ],
    'security' => [
        'block_sql_injection_patterns' => true,
        'block_xss_patterns' => true,
        'block_control_chars' => true,
        'require_email_dns' => false,
        'exclude_fields' => ['password', 'password_confirmation'],
    ],
    'exclude_fields' => ['_token', '_method', 'recaptcha_token'],
    'field_aliases' => [],
    'error_messages' => [
        'email' => 'The :attribute field must be a valid email address.',
        'phone' => 'The :attribute field must be a valid phone number.',
        'url' => 'The :attribute field must be a valid URL.',
        'uuid' => 'The :attribute field must be a valid UUID.',
        'ulid' => 'The :attribute field must be a valid ULID.',
        'ip' => 'The :attribute field must be a valid IP address.',
        'json' => 'The :attribute field must contain valid JSON.',
        'timezone' => 'The :attribute field must be a valid timezone.',
        'locale' => 'The :attribute field must be a valid locale code.',
        'strong_password' => 'The :attribute field is too weak. Must contain uppercase, lowercase, numbers, and special characters.',
        'credit_card' => 'The :attribute field must be a valid credit card number.',
        'not_regex' => 'The :attribute field contains blocked content patterns.',
    ],
];
