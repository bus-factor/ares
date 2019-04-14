<?php

declare(strict_types=1);

/**
 * ParserContextTest.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-13
 */

namespace UnitTest\Ares\Schema;

use Ares\Schema\ParserContext;
use PHPUnit\Framework\TestCase;

/**
 * Class ParserContextTest
 *
 * @coversDefaultClass \Ares\Schema\ParserContext
 */
class ParserContextTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getInput
     * @covers ::getInputPosition
     *
     * @return void
     */
    public function testConstruct(): void
    {
        $input = [];
        $relativeInputPosition = '';

        $parserContext = new ParserContext($input, $relativeInputPosition);

        $this->assertSame($input, $parserContext->getInput());
        $this->assertEquals([$relativeInputPosition], $parserContext->getInputPosition());
    }

    /**
     * @covers ::__construct
     * @covers ::enter
     * @covers ::getInput
     * @covers ::getInputPosition
     * @covers ::leave
     *
     * @return void
     */
    public function testEnterAndLeave(): void
    {
        $input = ['a' => ['b']];
        $relativeInputPosition = '';

        $parserContext = new ParserContext($input, $relativeInputPosition);

        $relativeInputPosition2 = 'a';

        $this->assertSame($parserContext, $parserContext->enter($relativeInputPosition2));
        $this->assertSame($input['a'], $parserContext->getInput());
        $this->assertEquals([$relativeInputPosition, $relativeInputPosition2], $parserContext->getInputPosition());

        $this->assertSame($parserContext, $parserContext->leave());
        $this->assertSame($input, $parserContext->getInput());
        $this->assertEquals([$relativeInputPosition], $parserContext->getInputPosition());
    }
}

