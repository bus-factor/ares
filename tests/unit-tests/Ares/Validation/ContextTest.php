<?php

declare(strict_types=1);

/**
 * ContextTest.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-16
 */

namespace UnitTest\Ares\Validation;

use Ares\Validation\Context;
use Ares\Validation\Error;
use PHPUnit\Framework\TestCase;

/**
 * Class ContextTest
 *
 * @coversDefaultClass \Ares\Validation\Context
 */
class ContextTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getData
     *
     * @return void
     */
    public function testDataAccessors(): void
    {
        $data = ['foo' => 'bar'];
        $context = new Context($data);

        $this->assertSame($data, $context->getData());
    }

    /**
     * @covers ::addError
     * @covers ::getErrors
     * @covers ::hasErrors
     *
     * @return void
     */
    public function testErrorsAccessors(): void
    {
        $context = new Context();

        $this->assertFalse($context->hasErrors());

        $context->enter('foo', []);

        $this->assertSame($context, $context->addError('rule_id', 'message'));
        $this->assertTrue($context->hasErrors());
        $this->assertEquals([new Error(['foo'], 'rule_id', 'message')], $context->getErrors());

        $context->enter('bar', []);

        $this->assertSame($context, $context->addError('other_rule_id', 'other_message'));
        $this->assertTrue($context->hasErrors());
        $this->assertEquals([new Error(['foo'], 'rule_id', 'message'), new Error(['foo', 'bar'], 'other_rule_id', 'other_message')], $context->getErrors());
    }

    /**
     * @covers ::enter
     * @covers ::getSchema
     * @covers ::getSource
     * @covers ::leave
     *
     * @return void
     */
    public function testContextNesting(): void
    {
        $context = new Context();

        $this->assertEquals([], $context->getSource());
        $this->assertSame($context, $context->enter('foo', ['a' => 1]));
        $this->assertEquals(['foo'], $context->getSource());
        $this->assertEquals(['a' => 1], $context->getSchema());
        $this->assertSame($context, $context->enter('bar', ['b' => 2]));
        $this->assertEquals(['foo', 'bar'], $context->getSource());
        $this->assertEquals(['b' => 2], $context->getSchema());
        $this->assertSame($context, $context->leave());
        $this->assertEquals(['foo'], $context->getSource());
        $this->assertEquals(['a' => 1], $context->getSchema());
        $this->assertSame($context, $context->leave());
        $this->assertEquals([], $context->getSource());
    }
}

