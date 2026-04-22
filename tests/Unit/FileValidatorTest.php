<?php

declare(strict_types=1);

namespace YourVendor\LaravelAutoValidator\Tests\Unit;

use YourVendor\LaravelAutoValidator\Validators\FileValidator;
use YourVendor\LaravelAutoValidator\Tests\TestCase;

class FileValidatorTest extends TestCase
{
    public function test_image_rule_contains_max_size_and_mimetypes(): void
    {
        $validator = new FileValidator(config('auto-validator'));
        $rules = $validator->rules('avatar_image');

        $this->assertContains('image', $rules);
        $this->assertTrue(collect($rules)->contains(fn ($rule) => str_starts_with((string) $rule, 'max:')));
    }
}
