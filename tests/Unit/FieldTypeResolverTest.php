<?php

declare(strict_types=1);

namespace YourVendor\LaravelAutoValidator\Tests\Unit;

use YourVendor\LaravelAutoValidator\Resolvers\FieldTypeResolver;
use YourVendor\LaravelAutoValidator\Tests\TestCase;

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
