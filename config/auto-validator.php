<?php

declare(strict_types=1);

return [
    'enabled' => env('AUTO_VALIDATOR_ENABLED', true),
    'auto_middleware' => env('AUTO_VALIDATOR_MIDDLEWARE', false),
    'strict_mode' => env('AUTO_VALIDATOR_STRICT', false),
    'sanitize' => [
        'trim' => true,
        'strip_tags' => true,
        'normalize_spaces' => true,
        'lowercase_email' => true,
        'format_phone' => true,
        'remove_currency_symbols' => true,
    ],
    'patterns' => [
        'email' => env('VALIDATOR_EMAIL_REGEX', '/^[^\s@]+@[^\s@]+\.[^\s@]+$/'),
        'phone' => env('VALIDATOR_PHONE_REGEX', '/^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/'),
        'url' => env('VALIDATOR_URL_REGEX', '/^https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b([-a-zA-Z0-9()@:%_\+.~#?&//=]*)$/'),
        'username' => '/^[a-zA-Z0-9_]{3,20}$/',
        'slug' => '/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
        'ipv4' => '/^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/',
        'ipv6' => '/^(([0-9a-fA-F]{1,4}:){7}[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,7}:|([0-9a-fA-F]{1,4}:){1,6}:[0-9a-fA-F]{1,4})$/',
        'uuid' => '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i',
        'credit_card' => '/^(?:4[0-9]{12}(?:[0-9]{3})?|5[1-5][0-9]{14}|3[47][0-9]{13})$/',
        'domain' => '/^(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z]{2,}$/',
        'mac_address' => '/^(?:[0-9a-fA-F]{2}:){5}[0-9a-fA-F]{2}$/',
        'json' => '/^\{[\s\S]*\}$|^\[[\s\S]*\]$/',
    ],
    'password_strength' => [
        'min_length' => 8,
        'require_uppercase' => true,
        'require_lowercase' => true,
        'require_numbers' => true,
        'require_special_chars' => true,
        'special_chars_pattern' => '/[!@#$%^&*()_+\-=\[\]{};:\'\",.<>?\/\\|`~]/',
    ],
    'file_uploads' => [
        'images' => [
            'extensions' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'],
            'mime_types' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'],
            'max_size' => 5120,
            'validate_dimensions' => true,
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
    'exclude_fields' => ['_token', '_method', 'recaptcha_token'],
    'field_aliases' => [],
    'error_messages' => [
        'email' => 'The :attribute field must be a valid email address.',
        'phone' => 'The :attribute field must be a valid phone number.',
        'strong_password' => 'The :attribute field is too weak. Must contain uppercase, lowercase, numbers, and special characters.',
        'credit_card' => 'The :attribute field must be a valid credit card number.',
    ],
];
