<?php

declare(strict_types=1);

/**
 * TypeRegistryTest.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-27
 */

namespace UnitTest\Ares\Schema;

use Ares\Schema\Rule;
use Ares\Schema\Schema;
use Ares\Schema\TypeRegistry;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Class TypeRegistryTest
 *
 * @coversDefaultClass \Ares\Schema\TypeRegistry
 */
class TypeRegistryTest extends TestCase
{
    /**
     * @covers ::get
     *
     * @return void
     */
    public function testGetHandlesUnknownTypes(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown type: type <Foo> is not registered');

        TypeRegistry::get('Foo');
    }

    /**
     * @covers ::get
     * @covers ::register
     *
     * @return void
     */
    public function testGet(): void
    {
        TypeRegistry::register('Foo', ['type' => 'integer']);

        $schema = TypeRegistry::get('Foo');
        $expectedSchema = (new Schema())->setRule(new Rule('type', 'integer'));

        $this->assertEquals($expectedSchema, $schema);

        TypeRegistry::unregister('Foo');
    }

    /**
     * @covers ::register
     *
     * @return void
     */
    public function testRegisterSecuresBuiltInTypes(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Builtin types must not be overwritten: integer');

        TypeRegistry::register('integer', ['type' => 'integer']);
    }

    /**
     * @covers ::isRegistered
     * @covers ::register
     * @covers ::unregister
     *
     * @return void
     */
    public function testRegistrationAccessors(): void
    {
        $this->assertFalse(TypeRegistry::isRegistered('Foo'));

        TypeRegistry::register('Foo', ['type' => 'integer']);

        $this->assertTrue(TypeRegistry::isRegistered('Foo'));

        TypeRegistry::unregister('Foo');

        $this->assertFalse(TypeRegistry::isRegistered('Foo'));
    }

    /**
     * @covers ::unregister
     *
     * @return void
     */
    public function testUnregisterHandlesUnknownTypes(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown type: cannot unregister <Foo>');

        TypeRegistry::unregister('Foo');
    }

    /**
     * @covers ::register
     * @covers ::unregisterAll
     *
     * @return void
     */
    public function testUnregisterAll(): void
    {
        TypeRegistry::register('Foo', ['type' => 'string']);
        TypeRegistry::register('Bar', ['type' => 'integer']);

        $this->assertTrue(TypeRegistry::isRegistered('Foo'));
        $this->assertTrue(TypeRegistry::isRegistered('Bar'));

        TypeRegistry::unregisterAll();

        $this->assertFalse(TypeRegistry::isRegistered('Foo'));
        $this->assertFalse(TypeRegistry::isRegistered('Bar'));
    }
}

