<?php

declare(strict_types=1);

namespace Greelogix\RequestGuardian\Tests\Unit;

use Greelogix\RequestGuardian\Validators\NumericValidator;
use Greelogix\RequestGuardian\Tests\TestCase;

class NumericValidatorTest extends TestCase
{
    public function test_it_returns_percentage_rules(): void
    {
        $validator = new NumericValidator(config('auto-validator'));

        $this->assertSame(['numeric', 'between:0,100'], $validator->rules('discount_percentage', 10));
    }
}
