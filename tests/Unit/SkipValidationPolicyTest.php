<?php

declare(strict_types=1);

namespace Greelogix\RequestGuardian\Tests\Unit;

use Illuminate\Http\Request;
use Greelogix\RequestGuardian\Support\SkipValidationPolicy;
use Greelogix\RequestGuardian\Tests\TestCase;

class SkipValidationPolicyTest extends TestCase
{
    public function test_filters_rules_from_route_field_and_type_configuration(): void
    {
        config()->set('auto-validator.skip_validation.rules', ['regex']);
        config()->set('auto-validator.skip_validation.field_rules', ['password' => ['max']]);
        config()->set('auto-validator.skip_validation.type_rules', ['password' => ['strong_password']]);
        config()->set('auto-validator.skip_validation.route_rules', ['login' => ['string']]);

        $policy = new SkipValidationPolicy();
        $request = Request::create('/login', 'POST');

        $result = $policy->filterRules(
            $request,
            'password',
            'password',
            ['string', 'strong_password', 'max:255', 'regex:/foo/']
        );

        $this->assertSame([], $result);
    }
}
