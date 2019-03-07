<?php

declare(strict_types=1);

/**
 * ErrorTest.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-02-24
 */

namespace UnitTest\Ares\Validation;

use Ares\Validation\Error;
use PHPUnit\Framework\TestCase;

/**
 * Class ErrorTest
 *
 * @coversDefaultClass \Ares\Validation\Error
 * @uses \Ares\Utility\JsonPointer
 */
class ErrorTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getCode
     * @covers ::getMessage
     * @covers ::getMeta
     * @covers ::getSource
     *
     * @return void
     */
    public function testConstruct(): void
    {
        $code = 'required';
        $message = 'Value required';
        $meta = ['type' => 'error'];
        $source = ['data', 'email'];

        $error = new Error($source, $code, $message, $meta);

        $this->assertSame($code, $error->getCode());
        $this->assertSame($message, $error->getMessage());
        $this->assertSame($meta, $error->getMeta());
        $this->assertSame($source, $error->getSource());
    }

    /**
     * @covers ::getCode
     * @covers ::getMessage
     * @covers ::getMeta
     * @covers ::getSource
     * @covers ::setCode
     * @covers ::setMessage
     * @covers ::setMeta
     * @covers ::setSource
     *
     * @testWith ["getCode", "setCode", "unique"]
     *           ["getMessage", "setMessage", "Foo bar"]
     *           ["getMeta", "setMeta", {"a": "test"}]
     *           ["getSource", "setSource", ["data", "foo", "bar"]]
     *
     * @param string $getterName
     * @param string $setterName
     * @param mixed $newValue
     * @return void
     */
    public function testAccessors(string $getterName, string $setterName, $newValue): void
    {
        $code = 'required';
        $message = 'Value required';
        $meta = ['type' => 'error'];
        $source = ['data', 'email'];

        $error = new Error($source, $code, $message, $meta);

        $this->assertSame($error, $error->{$setterName}($newValue));
        $this->assertSame($newValue, $error->{$getterName}());
    }

    /**
     * @covers ::jsonSerialize
     *
     * @return void
     */
    public function testJsonSerialize(): void
    {
        $code = 'required';
        $message = 'Value required';
        $meta = ['type' => 'error'];
        $source = ['data', 'email'];

        $error = new Error($source, $code, $message, $meta);

        $this->assertEquals(
            [
                'code' => $code,
                'message' => $message,
                'meta' => $meta,
                'source' => $source,
            ],
            $error->jsonSerialize()
        );
    }
}

