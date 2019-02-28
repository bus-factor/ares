<?php

declare(strict_types=1);

/**
 * JsonPointerTest.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-02-28
 */

namespace UnitTest\Ares\Utility;

use Ares\Exception\StackEmptyException;
use Ares\Utility\JsonPointer;
use PHPUnit\Framework\TestCase;

/**
 * Class JsonPointerTest
 *
 * @coversDefaultClass \Ares\Utility\JsonPointer
 */
class JsonPointerTest extends TestCase
{
    /**
     * @covers ::empty
     * @covers ::pop
     * @covers ::push
     *
     * @testWith [[], 0, true]
     *           [[""], 0, false]
     *           [[""], 1, true]
     *           [["foo"], 0, false]
     *           [["foo"], 1, true]
     *           [["foo", "bar"], 0, false]
     *           [["foo", "bar"], 1, false]
     *           [["foo", "bar"], 2, true]
     *
     * @param array   $references              JSON pointer references.
     * @param integer $numberOfReferencesToPop Number of references to pop.
     * @param boolean $expectedRetVal          Expected return value.
     * @return void
     */
    public function testEmpty(array $references, int $numberOfReferencesToPop, bool $expectedRetVal): void
    {
        $jsonPointer = new JsonPointer();

        foreach ($references as $reference) {
            $jsonPointer->push($reference);
        }

        for ($i = 0; $i < $numberOfReferencesToPop; $i++) {
            $jsonPointer->pop();
        }

        $this->assertSame($expectedRetVal, $jsonPointer->empty());
    }

    /**
     * @covers ::fromString
     * @covers ::toArray
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
            $this->assertEquals($expectedReferences, $jsonPointer->toArray());
        }
    }

    /**
     * @covers ::peek
     * @covers ::pop
     * @covers ::push
     *
     * @testWith [[""], 0, ""]
     *           [["foo"], 0, "foo"]
     *           [["foo", "bar"], 0, "bar"]
     *           [["foo", "bar"], 1, "foo"]
     *
     * @param array   $references              JSON pointer references.
     * @param integer $numberOfReferencesToPop Number of references to pop.
     * @param string  $expectedRetVal          Expected return value.
     * @return void
     */
    public function testPeek(array $references, int $numberOfReferencesToPop, string $expectedRetVal): void
    {
        $jsonPointer = new JsonPointer();

        foreach ($references as $reference) {
            $jsonPointer->push($reference);
        }

        for ($i = 0; $i < $numberOfReferencesToPop; $i++) {
            $jsonPointer->pop();
        }

        $this->assertSame($expectedRetVal, $jsonPointer->peek());
    }

    /**
     * @covers ::peek
     *
     * @return void
     */
    public function testPeekThrowsStackEmptyException(): void
    {
        $this->expectException(StackEmptyException::class);

        (new JsonPointer())->peek();
    }

    /**
     * @covers ::pop
     * @covers ::push
     * @covers ::toArray
     *
     * @testWith [[], 0, []]
     *           [[""], 0, [""]]
     *           [[""], 1, []]
     *           [["foo"], 0, ["foo"]]
     *           [["foo"], 1, []]
     *           [["foo", "bar"], 0, ["foo", "bar"]]
     *           [["foo", "bar"], 1, ["foo"]]
     *           [["foo", "bar"], 2, []]
     *
     * @param array   $references              JSON pointer references.
     * @param integer $numberOfReferencesToPop Number of references to pop.
     * @param array   $expectedRetVal          Expected return value.
     * @return void
     */
    public function testToArray(array $references, int $numberOfReferencesToPop, array $expectedRetVal): void
    {
        $jsonPointer = new JsonPointer();

        foreach ($references as $reference) {
            $jsonPointer->push($reference);
        }

        for ($i = 0; $i < $numberOfReferencesToPop; $i++) {
            $jsonPointer->pop();
        }

        $this->assertEquals($expectedRetVal, $jsonPointer->toArray());
    }

    /**
     * @covers ::pop
     * @covers ::push
     * @covers ::toString
     *
     * @testWith [[], 0, null]
     *           [[""], 0, ""]
     *           [["foo"], 0, "foo"]
     *           [["foo"], 1, null]
     *           [["", "foo", "bar"], 0, "/foo/bar"]
     *           [["", "foo", "bar"], 1, "/foo"]
     *           [["", "foo", "bar"], 2, ""]
     *           [["a/b", "~a/~b"], 0, "a~1b/~0a~1~0b"]
     *
     * @param array       $references              JSON pointer references.
     * @param integer     $numberOfReferencesToPop Number of references to pop.
     * @param string|null $expectedRetVal          Expected return value.
     * @return void
     */
    public function testToString(array $references, int $numberOfReferencesToPop, $expectedRetVal): void
    {
        $jsonPointer = new JsonPointer();

        foreach ($references as $reference) {
            $jsonPointer->push($reference);
        }

        for ($i = 0; $i < $numberOfReferencesToPop; $i++) {
            $jsonPointer->pop();
        }

        $this->assertEquals($expectedRetVal, $jsonPointer->toString());
    }
}

