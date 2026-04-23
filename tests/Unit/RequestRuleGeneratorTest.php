<?php

declare(strict_types=1);

namespace Greelogix\RequestGuardian\Tests\Unit;

use Illuminate\Http\Request;
use Greelogix\RequestGuardian\Resolvers\FieldTypeResolver;
use Greelogix\RequestGuardian\Services\RequestRuleGenerator;
use Greelogix\RequestGuardian\Support\SkipValidationPolicy;
use Greelogix\RequestGuardian\Tests\TestCase;

class RequestRuleGeneratorTest extends TestCase
{
    public function test_applies_route_rule_skips_when_generating_rules(): void
    {
        config()->set('auto-validator.skip_validation.route_rules', ['login' => ['strong_password']]);

        $generator = new RequestRuleGenerator(
            app(FieldTypeResolver::class),
            app(SkipValidationPolicy::class)
        );

        $request = Request::create('/login', 'POST', [
            'email' => 'john@example.com',
            'password' => 'password',
        ]);

        $rules = $generator->generate($request);

        $this->assertArrayHasKey('password', $rules);
        $this->assertContains('string', $rules['password']);
        $this->assertNotContains('strong_password', $rules['password']);
    }
}
