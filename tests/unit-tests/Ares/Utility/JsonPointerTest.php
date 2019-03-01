<?php

declare(strict_types=1);

/**
 * JsonPointerTest.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-02-28
 */

namespace UnitTest\Ares\Utility;

use Ares\Utility\JsonPointer;
use PHPUnit\Framework\TestCase;

/**
 * Class JsonPointerTest
 *
 * @coversDefaultClass \Ares\Utility\JsonPointer
 * @uses \Ares\Utility\Stack
 */
class JsonPointerTest extends TestCase
{
    /**
     * @testWith ["Ares\\Utility\\Stack"]
     *
     * @param string $fqcn Fully-qualified class or interface name.
     * @return void
     */
    public function testInstanceOf(string $fqcn): void
    {
        $jsonPointer = new JsonPointer();

        $this->assertInstanceOf($fqcn, $jsonPointer);
    }

    /**
     * @covers ::decodeReference
     * @covers ::fromString
     *
     * @testWith [null, null]
     *           ["", [""]]
     *           ["foo/bar", ["foo", "bar"]]
     *           [ "a~1b/~0a~1~0b", ["a/b", "~a/~b"]]
     *
     * @param string|null $jsonPointerString String-formatted JSON pointer.
     * @param array       $references        References.
     * @return void
     */
    public function testFromString(?string $jsonPointerString, ?array $expectedReferences): void
    {
        $jsonPointer = JsonPointer::fromString($jsonPointerString);

        if ($jsonPointerString === $expectedReferences) {
            $this->assertTrue(true);
        } else {
            $this->assertEquals($expectedReferences, $jsonPointer->getElements());
        }
    }

    /**
     * @covers ::jsonSerialize
     *
     * @return void
     */
    public function testJsonSerialize(): void
    {
        $jsonPointerString = '/foo/0/bar';

        $jsonPointer = $this->getMockBuilder(JsonPointer::class)
            ->setMethods(['toString'])
            ->getMock();

        $jsonPointer->expects($this->once())
            ->method('toString')
            ->willReturn($jsonPointerString);

        $this->assertSame($jsonPointerString, $jsonPointer->jsonSerialize());
    }

    /**
     * @covers ::encodeReference
     * @covers ::toString
     *
     * @testWith [[], null]
     *           [[""], ""]
     *           [["foo"], "foo"]
     *           [["", "foo"], "/foo"]
     *           [["", "foo", "bar"], "/foo/bar"]
     *           [["a/b", "~a/~b"], "a~1b/~0a~1~0b"]
     *
     * @param array       $references     JSON pointer references.
     * @param string|null $expectedRetVal Expected return value.
     * @return void
     */
    public function testToString(array $references, ?string $expectedRetVal): void
    {
        $jsonPointer = new JsonPointer($references);

        $this->assertEquals($expectedRetVal, $jsonPointer->toString());
    }
}

