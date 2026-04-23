<?php

declare(strict_types=1);

namespace Greelogix\RequestGuardian\Resolvers;

class FieldTypeResolver
{
    private const DEFAULT_DETECTION_KEYWORDS = [
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
    ];

    public function __construct(private readonly array $config)
    {
    }

    public function resolve(string $field, mixed $value): string
    {
        $overrides = $this->arrayConfig('field_types');
        if (isset($overrides[$field])) {
            return (string) $overrides[$field];
        }

        $aliases = $this->arrayConfig('field_aliases');
        if (isset($aliases[$field])) {
            return (string) $aliases[$field];
        }

        if (is_array($value)) {
            return 'array';
        }

        $normalized = strtolower($field);

        $map = $this->arrayConfig('detection_keywords', self::DEFAULT_DETECTION_KEYWORDS);

        foreach ($map as $type => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($normalized, $keyword)) {
                    return $type;
                }
            }
        }

        if (is_bool($value)) {
            return 'boolean';
        }

        if (is_int($value)) {
            return 'integer';
        }

        if (is_float($value)) {
            return 'decimal';
        }

        return 'string';
    }

    public function rulesForType(string $type, string $field, mixed $value): array
    {
        $emailRule = $this->boolConfig('security.require_email_dns')
            ? 'email:rfc,dns'
            : 'email:rfc';

        $customTypeRules = $this->arrayConfig('custom_type_rules');
        if (isset($customTypeRules[$type]) && is_array($customTypeRules[$type])) {
            $rules = $customTypeRules[$type];
        } else {
            $rules = match ($type) {
            'email' => ['string', $emailRule, 'max:' . (int) $this->configValue('string_limits.email_max', 254), 'regex:' . $this->configValue('patterns.email', '/^[^\s@]+@[^\s@]+\.[^\s@]+$/')],
            'phone' => ['string', 'regex:' . $this->configValue('patterns.phone', '/^\+?[0-9\s\-().]{7,20}$/')],
            'url' => ['string', 'url:https,http'],
            'ip' => ['ip'],
            'ipv4' => ['ipv4', 'regex:' . $this->configValue('patterns.ipv4', '/.*/')],
            'ipv6' => ['ipv6', 'regex:' . $this->configValue('patterns.ipv6', '/.*/')],
            'password' => ['string', 'strong_password', 'max:' . (int) $this->configValue('string_limits.password_max', 255)],
            'username' => ['string', 'min:' . (int) $this->configValue('string_limits.username_min', 3), 'max:' . (int) $this->configValue('string_limits.username_max', 32), 'regex:' . $this->configValue('patterns.username', '/^[a-zA-Z0-9_]{3,20}$/')],
            'name' => ['string', 'min:1', 'max:' . (int) $this->configValue('string_limits.name_max', 120), 'regex:' . $this->configValue('patterns.name', '/^[\pL\s\-\'\.]+$/u')],
            'date' => ['date'],
            'time' => ['date_format:H:i'],
            'datetime' => ['date'],
            'timestamp' => ['integer', 'min:0'],
            'uuid' => ['string', 'uuid', 'regex:' . $this->configValue('patterns.uuid', '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i')],
            'ulid' => ['string', 'ulid'],
            'slug' => ['string', 'min:1', 'max:' . (int) $this->configValue('string_limits.slug_max', 150), 'regex:' . $this->configValue('patterns.slug', '/^[a-z0-9]+(?:-[a-z0-9]+)*$/')],
            'domain' => ['string', 'regex:' . $this->configValue('patterns.domain', '/^(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z]{2,}$/')],
            'mac_address' => ['string', 'regex:' . $this->configValue('patterns.mac_address', '/^(?:[0-9a-fA-F]{2}:){5}[0-9a-fA-F]{2}$/')],
            'credit_card' => ['string', 'digits_between:12,19', 'regex:' . $this->configValue('patterns.credit_card', '/^(?:4[0-9]{12}(?:[0-9]{3})?|5[1-5][0-9]{14}|3[47][0-9]{13})$/')],
            'image' => $this->fileRules('images', ['file', 'image']),
            'document' => $this->fileRules('documents', ['file']),
            'video' => $this->fileRules('videos', ['file']),
            'audio' => $this->fileRules('audio', ['file']),
            'file' => ['file'],
            'array' => ['array', 'max:' . (int) $this->configValue('array_limits.max_items', 2000)],
            'integer' => ['integer', 'between:' . (int) $this->configValue('numeric_limits.integer_min', -2147483648) . ',' . (int) $this->configValue('numeric_limits.integer_max', 2147483647)],
            'decimal' => ['numeric'],
            'currency' => ['numeric', 'min:' . (float) $this->configValue('numeric_limits.currency_min', 0), 'max:' . (float) $this->configValue('numeric_limits.currency_max', 999999999.99)],
            'percentage' => ['numeric', 'between:0,100'],
            'boolean' => ['boolean'],
            'json' => ['string', 'json', 'regex:' . $this->configValue('patterns.json', '/^\{[\s\S]*\}$|^\[[\s\S]*\]$/')],
            'timezone' => ['string', 'timezone'],
            'locale' => ['string', 'regex:' . $this->configValue('patterns.locale', '/^[a-z]{2}([_-][A-Z]{2})?$/')],
            'color' => ['string', 'regex:' . $this->configValue('patterns.hex_color', '/^#(?:[0-9a-fA-F]{3}){1,2}$/')],
            'csv' => ['string', 'regex:' . $this->configValue('patterns.csv', '/^[^,\n]+(,[^,\n]+)*$/')],
            'postal_code' => ['string', 'regex:' . $this->configValue('patterns.postal_code', '/^[A-Za-z0-9\-\s]{3,12}$/')],
            'enum' => $this->enumRules($field),
            default => ['string', 'max:' . (int) $this->configValue('string_limits.default_max', 65535)],
        };
        }

        return $this->applySecurityRules($field, $rules);
    }

    private function fileRules(string $bucket, array $base): array
    {
        $settings = $this->arrayConfig('file_uploads.' . $bucket);
        $rules = $base;

        if (!empty($settings['mime_types'])) {
            $rules[] = 'mimetypes:' . implode(',', (array) $settings['mime_types']);
        }

        if (!empty($settings['extensions'])) {
            $rules[] = 'extensions:' . implode(',', (array) $settings['extensions']);
        }

        $rules[] = 'max:' . (int) ($settings['max_size'] ?? 5120);

        return $rules;
    }

    private function enumRules(string $field): array
    {
        $enumFields = $this->arrayConfig('enum_fields');
        $allowed = $enumFields[$field] ?? [];

        if ($allowed === []) {
            return ['string'];
        }

        return ['string', 'in:' . implode(',', (array) $allowed)];
    }

    private function applySecurityRules(string $field, array $rules): array
    {
        $security = $this->arrayConfig('security');
        $skip = (array) ($security['exclude_fields'] ?? []);

        if (in_array($field, $skip, true)) {
            return $rules;
        }

        if (($security['block_sql_injection_patterns'] ?? true) === true) {
            $rules[] = 'not_regex:/(\bunion\b|\bselect\b|\binsert\b|\bdelete\b|\bdrop\b|\b--\b|;)/i';
        }

        if (($security['block_xss_patterns'] ?? true) === true) {
            $rules[] = 'not_regex:/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/i';
        }

        if (($security['block_control_chars'] ?? true) === true) {
            $rules[] = 'not_regex:/[\x00-\x1F\x7F]/';
        }

        return array_values(array_unique($rules));
    }

    private function configValue(string $path, mixed $default = null): mixed
    {
        $value = data_get($this->config, $path, $default);

        return $value ?? $default;
    }

    private function arrayConfig(string $path, array $default = []): array
    {
        $value = $this->configValue($path, $default);

        return is_array($value) ? $value : $default;
    }

    private function boolConfig(string $path, bool $default = false): bool
    {
        return (bool) $this->configValue($path, $default);
    }
}
