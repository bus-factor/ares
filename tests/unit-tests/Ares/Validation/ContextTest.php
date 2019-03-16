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

        $context->pushSourceReference('foo');

        $this->assertSame($context, $context->addError('rule_id', 'message'));
        $this->assertTrue($context->hasErrors());
        $this->assertEquals([new Error(['foo'], 'rule_id', 'message')], $context->getErrors());

        $context->pushSourceReference('bar');

        $this->assertSame($context, $context->addError('other_rule_id', 'other_message'));
        $this->assertTrue($context->hasErrors());
        $this->assertEquals([new Error(['foo'], 'rule_id', 'message'), new Error(['foo', 'bar'], 'other_rule_id', 'other_message')], $context->getErrors());
    }

    /**
     * @covers ::getSource
     * @covers ::popSourceReference
     * @covers ::pushSourceReference
     *
     * @return void
     */
    public function testSourceAccessors(): void
    {
        $context = new Context();

        $this->assertEquals([], $context->getSource());
        $this->assertSame($context, $context->pushSourceReference('foo'));
        $this->assertEquals(['foo'], $context->getSource());
        $this->assertSame($context, $context->pushSourceReference('bar'));
        $this->assertEquals(['foo', 'bar'], $context->getSource());
        $this->assertSame($context, $context->popSourceReference());
        $this->assertEquals(['foo'], $context->getSource());
        $this->assertSame($context, $context->popSourceReference());
        $this->assertEquals([], $context->getSource());
    }
}

