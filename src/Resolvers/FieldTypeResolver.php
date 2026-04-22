<?php

declare(strict_types=1);

namespace YourVendor\LaravelAutoValidator\Resolvers;

class FieldTypeResolver
{
    public function __construct(private readonly array $config)
    {
    }

    public function resolve(string $field, mixed $value): string
    {
        $aliases = $this->config['field_aliases'] ?? [];
        if (isset($aliases[$field])) {
            return (string) $aliases[$field];
        }

        if (is_array($value)) {
            return 'array';
        }

        $normalized = strtolower($field);

        $map = [
            'email' => ['email', 'mail'],
            'phone' => ['phone', 'mobile', 'cell', 'contact_number'],
            'url' => ['url', 'website', 'link'],
            'ip' => ['ip', 'ipv4', 'ipv6'],
            'password' => ['password', 'pwd', 'pass'],
            'date' => ['date', 'dob', 'birthdate'],
            'time' => ['time'],
            'uuid' => ['uuid'],
            'slug' => ['slug', 'permalink'],
            'image' => ['image', 'avatar', 'photo', 'picture'],
            'file' => ['file', 'upload', 'attachment'],
            'integer' => ['age', 'count', 'qty', 'quantity', 'number'],
            'currency' => ['amount', 'price', 'cost', 'total'],
            'percentage' => ['percent', 'percentage', 'rate'],
            'boolean' => ['is_', 'has_', 'can_', 'should_', 'active', 'enabled'],
            'json' => ['json', 'payload', 'meta'],
        ];

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
        return match ($type) {
            'email' => ['string', 'regex:' . ($this->config['patterns']['email'] ?? '/^[^\s@]+@[^\s@]+\.[^\s@]+$/')],
            'phone' => ['string', 'regex:' . ($this->config['patterns']['phone'] ?? '/^\+?[0-9\s\-().]{7,20}$/')],
            'url' => ['string', 'url'],
            'ip' => ['ip'],
            'password' => ['string', 'strong_password'],
            'date' => ['date'],
            'time' => ['date_format:H:i'],
            'uuid' => ['string', 'uuid'],
            'slug' => ['string', 'regex:' . ($this->config['patterns']['slug'] ?? '/^[a-z0-9]+(?:-[a-z0-9]+)*$/')],
            'image' => ['file', 'image', 'max:' . (int) ($this->config['file_uploads']['images']['max_size'] ?? 5120)],
            'file' => ['file'],
            'array' => ['array'],
            'integer' => ['integer'],
            'decimal' => ['numeric'],
            'currency' => ['numeric', 'min:0'],
            'percentage' => ['numeric', 'between:0,100'],
            'boolean' => ['boolean'],
            'json' => ['string', 'json'],
            default => ['string', 'max:65535'],
        };
    }
}
