<?php

declare(strict_types=1);

namespace Greelogix\RequestGuardian\Tests\Unit;

use Greelogix\RequestGuardian\Validators\StringValidator;
use Greelogix\RequestGuardian\Tests\TestCase;

class StringValidatorTest extends TestCase
{
    public function test_supports_string_based_types(): void
    {
        $validator = new StringValidator(config('auto-validator'));

        $this->assertTrue($validator->supports('email'));
        $this->assertTrue($validator->supports('string'));
        $this->assertFalse($validator->supports('integer'));
    }
}
