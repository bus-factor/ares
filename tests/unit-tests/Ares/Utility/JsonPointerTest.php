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
use InvalidArgumentException;

/**
 * Class JsonPointerTest
 *
 * @coversDefaultClass \Ares\Utility\JsonPointer
 */
class JsonPointerTest extends TestCase
{
    /**
     * @covers ::decode
     *
     * @testWith ["", [""]]
     *           ["foo/bar", ["foo", "bar"]]
     *           [ "a~1b/~0a~1~0b", ["a/b", "~a/~b"]]
     *
     * @param string $jsonPointer        JSON pointer.
     * @param array  $expectedReferences References.
     * @return void
     */
    public function testDecode(string $jsonPointer, array $expectedReferences): void
    {
        $this->assertEquals($expectedReferences, JsonPointer::decode($jsonPointer));
    }

    /**
     * @covers ::encode
     *
     * @testWith [[""], ""]
     *           [["foo"], "foo"]
     *           [["", "foo"], "/foo"]
     *           [["", "foo", "bar"], "/foo/bar"]
     *           [["a/b", "~a/~b"], "a~1b/~0a~1~0b"]
     *
     * @param array  $references          References.
     * @param string $expectedJsonPointer Expected JSON pointer.
     * @return void
     */
    public function testEncode(array $references, string $expectedJsonPointer): void
    {
        $this->assertEquals($expectedJsonPointer, JsonPointer::encode($references));
    }

    /**
     * @covers ::encode
     *
     * @return void
     */
    public function testEncodeHandlesMissingReferences(): void
    {
        $this->expectException(InvalidArgumentException::class);

        JsonPointer::encode([]);
    }
}

