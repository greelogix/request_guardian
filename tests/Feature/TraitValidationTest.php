<?php

declare(strict_types=1);

namespace Greelogix\RequestGuardian\Tests\Feature;

use Illuminate\Http\Request;
use Greelogix\RequestGuardian\Tests\TestCase;
use Greelogix\RequestGuardian\Traits\AutoValidatesTrait;

class TraitValidationTest extends TestCase
{
    public function test_auto_validate_generates_rules_and_validates_payload(): void
    {
        $harness = new class {
            use AutoValidatesTrait;
        };

        $request = Request::create('/users', 'POST', [
            'user_email' => 'john@example.com',
            'contact_phone' => '5551234567',
            'display_name' => 'John Doe',
        ]);

        $validator = $harness->autoValidate($request);

        $this->assertFalse($validator->fails());
        $this->assertArrayHasKey('user_email', $validator->validated());
    }
}
