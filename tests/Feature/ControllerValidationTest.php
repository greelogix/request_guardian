<?php

declare(strict_types=1);

namespace YourVendor\LaravelAutoValidator\Tests\Feature;

use Illuminate\Http\Request;
use YourVendor\LaravelAutoValidator\Resolvers\FieldTypeResolver;
use YourVendor\LaravelAutoValidator\Tests\TestCase;

class ControllerValidationTest extends TestCase
{
    public function test_resolver_rules_cover_common_controller_payload_fields(): void
    {
        $resolver = app(FieldTypeResolver::class);

        $emailRules = $resolver->rulesForType('email', 'email', 'john@example.com');
        $passwordRules = $resolver->rulesForType('password', 'password', 'Password123!');

        $this->assertNotEmpty($emailRules);
        $this->assertContains('strong_password', $passwordRules);
    }
}
