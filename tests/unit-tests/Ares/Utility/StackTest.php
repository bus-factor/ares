<?php

declare(strict_types=1);

/**
 * StackTest.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-01
 */

namespace UnitTest\Ares\Utility;

use Ares\Exception\StackEmptyException;
use Ares\Utility\Stack;
use PHPUnit\Framework\TestCase;

/**
 * Class StackTest
 *
 * @coversDefaultClass \Ares\Utility\Stack
 */
class StackTest extends TestCase
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
        $stack = new Stack($elements);

        $this->assertEquals($elements, $stack->getElements());
    }

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
     * @param array   $elements              Stack elements.
     * @param integer $numberOfElementsToPop Number of elements to pop.
     * @param boolean $expectedRetVal        Expected return value.
     * @return void
     */
    public function testEmpty(array $elements, int $numberOfElementsToPop, bool $expectedRetVal): void
    {
        $stack = new Stack();

        foreach ($elements as $element) {
            $stack->push($element);
        }

        for ($i = 0; $i < $numberOfElementsToPop; $i++) {
            $stack->pop();
        }

        $this->assertSame($expectedRetVal, $stack->empty());
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
     * @param array   $elements              Elements.
     * @param integer $numberOfElementsToPop Number of elements to pop.
     * @param string  $expectedRetVal        Expected return value.
     * @return void
     */
    public function testPeek(array $elements, int $numberOfElementsToPop, string $expectedRetVal): void
    {
        $stack = new Stack();

        foreach ($elements as $element) {
            $stack->push($element);
        }

        for ($i = 0; $i < $numberOfElementsToPop; $i++) {
            $stack->pop();
        }

        $this->assertSame($expectedRetVal, $stack->peek());
    }

    /**
     * @covers ::peek
     *
     * @return void
     */
    public function testPeekThrowsStackEmptyException(): void
    {
        $this->expectException(StackEmptyException::class);

        (new Stack())->peek();
    }

    /**
     * @covers ::pop
     *
     * @return void
     */
    public function testPopThrowsStackEmptyException(): void
    {
        $this->expectException(StackEmptyException::class);

        (new Stack())->pop();
    }

    /**
     * @covers ::pop
     * @covers ::push
     * @covers ::getElements
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
     * @param array   $elements              Elements.
     * @param integer $numberOfElementsToPop Number of elements to pop.
     * @param array   $expectedRetVal        Expected return value.
     * @return void
     */
    public function testGetElements(array $elements, int $numberOfElementsToPop, array $expectedRetVal): void
    {
        $stack = new Stack();

        foreach ($elements as $element) {
            $stack->push($element);
        }

        for ($i = 0; $i < $numberOfElementsToPop; $i++) {
            $stack->pop();
        }

        $this->assertEquals($expectedRetVal, $stack->getElements());
    }

    /**
     * @covers ::getElements
     * @covers ::setElements
     *
     * @return void
     */
    public function testSetElements(): void
    {
        $elements = ['foo', 'bar'];
        $stack = new Stack();

        $this->assertSame($stack, $stack->setElements($elements));
        $this->assertEquals($elements, $stack->getElements());
    }
}

