<?php

declare(strict_types=1);

/**
 * StackOfStringsTest.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-01
 */

namespace UnitTest\Ares\Utility;

use Ares\Exception\StackEmptyException;
use Ares\Utility\StackOfStrings;
use PHPUnit\Framework\TestCase;

/**
 * Class StackOfStringsTest
 *
 * @coversDefaultClass \Ares\Utility\StackOfStrings
 */
class StackOfStringsTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getElements
     * @covers ::setElements
     *
     * @testWith [[]]
     *           [["foo", "bar"]]
     *
     * @param array|null $elements
     * @return void
     */
    public function testConstruct(?array $elements): void
    {
        $stackOfStrings = new StackOfStrings($elements);

        $this->assertEquals($elements, $stackOfStrings->getElements());
    }

    /**
     * @covers ::empty
     * @covers ::push
     *
     * @testWith [[], true]
     *           [[""], false]
     *           [["foo"], false]
     *           [["foo", "bar"], false]
     *
     * @param array   $elements       StackOfStrings elements.
     * @param boolean $expectedRetVal Expected return value.
     * @return void
     */
    public function testEmpty(array $elements, bool $expectedRetVal): void
    {
        $stackOfStrings = new StackOfStrings($elements);

        $this->assertSame($expectedRetVal, $stackOfStrings->empty());
    }

    /**
     * @covers ::peek
     * @covers ::push
     *
     * @testWith [[""], ""]
     *           [["foo"], "foo"]
     *           [["foo", "bar"], "bar"]
     *
     * @param array  $elements       Elements.
     * @param string $expectedRetVal Expected return value.
     * @return void
     */
    public function testPeek(array $elements, string $expectedRetVal): void
    {
        $stackOfStrings = new StackOfStrings($elements);

        $this->assertSame($expectedRetVal, $stackOfStrings->peek());
    }

    /**
     * @covers ::peek
     *
     * @return void
     */
    public function testPeekThrowsStackOfStringsEmptyException(): void
    {
        $this->expectException(StackEmptyException::class);

        (new StackOfStrings())->peek();
    }

    /**
     * @covers ::pop
     *
     * @testWith [[""], 1, []]
     *           [["foo"], 1, []]
     *           [["foo", "bar"], 1, ["foo"]]
     *           [["foo", "bar"], 2, []]
     *
     * @param array   $elements              Initial stackOfStrings elements.
     * @param integer $numberOfElementsToPop Number of elements to pop.
     * @param array   $expectedElements      Expected stackOfStrings elements.
     * @return void
     */
    public function testPop(array $elements, int $numberOfElementsToPop, array $expectedElements): void
    {
        $stackOfStrings = new StackOfStrings($elements);

        for ($i = 0; $i < $numberOfElementsToPop; $i++) {
            $stackOfStrings->pop();
        }

        $this->assertEquals($expectedElements, $stackOfStrings->getElements());
    }

    /**
     * @covers ::pop
     *
     * @return void
     */
    public function testPopThrowsStackOfStringsEmptyException(): void
    {
        $this->expectException(StackEmptyException::class);

        (new StackOfStrings())->pop();
    }

    /**
     * @covers ::push
     * @covers ::getElements
     *
     * @testWith [[], []]
     *           [[""], [""]]
     *           [["foo"], ["foo"]]
     *           [["foo", "bar"], ["foo", "bar"]]
     *
     * @param array $elements       Elements.
     * @param array $expectedRetVal Expected return value.
     * @return void
     */
    public function testGetElements(array $elements, array $expectedRetVal): void
    {
        $stackOfStrings = new StackOfStrings($elements);

        $this->assertEquals($expectedRetVal, $stackOfStrings->getElements());
    }

    /**
     * @covers ::getElements
     * @covers ::setElements
     *
     * @return void
     */
    public function testSetElementsChangesTheElements(): void
    {
        $elements = ['foo', 'bar'];
        $stackOfStrings = new StackOfStrings();

        $stackOfStrings->setElements($elements);

        $this->assertEquals($elements, $stackOfStrings->getElements());
    }

    /**
     * @covers ::getElements
     * @covers ::setElements
     *
     * @return void
     */
    public function testSetElementsReturnsSelfReference(): void
    {
        $stackOfStrings = new StackOfStrings();
        $otherStackOfStrings = $stackOfStrings->setElements(['foo', 'bar']);

        $this->assertSame($stackOfStrings, $otherStackOfStrings);
    }
}

