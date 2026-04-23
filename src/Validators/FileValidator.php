<?php

declare(strict_types=1);

namespace Greelogix\RequestGuardian\Validators;

class FileValidator extends BaseValidator
{
    public function supports(string $type): bool
    {
        return in_array($type, ['file', 'image', 'document', 'video', 'audio'], true);
    }

    public function rules(string $field, mixed $value = null): array
    {
        if (preg_match('/image|avatar|photo|picture/i', $field)) {
            $image = $this->config['file_uploads']['images'] ?? [];
            return [
                'file',
                'image',
                'mimetypes:' . implode(',', $image['mime_types'] ?? ['image/jpeg', 'image/png']),
                'max:' . (int) ($image['max_size'] ?? 5120),
            ];
        }

        return ['file'];
    }
}
