<?php

declare(strict_types=1);

namespace Greelogix\RequestGuardian\Tests\Unit;

use Greelogix\RequestGuardian\Validators\FileValidator;
use Greelogix\RequestGuardian\Tests\TestCase;

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
