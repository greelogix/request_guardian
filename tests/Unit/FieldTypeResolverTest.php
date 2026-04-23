<?php

declare(strict_types=1);

namespace Greelogix\RequestGuardian\Tests\Unit;

use Greelogix\RequestGuardian\Resolvers\FieldTypeResolver;
use Greelogix\RequestGuardian\Tests\TestCase;

class FieldTypeResolverTest extends TestCase
{
    public function test_it_resolves_common_field_types(): void
    {
        $resolver = new FieldTypeResolver(config('auto-validator'));

        $this->assertSame('email', $resolver->resolve('user_email', 'john@example.com'));
        $this->assertSame('phone', $resolver->resolve('contact_phone', '5551231234'));
        $this->assertSame('array', $resolver->resolve('contacts', [['email' => 'a@b.com']]));
    }
}
