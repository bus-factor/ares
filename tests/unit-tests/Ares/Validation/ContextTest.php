<?php

declare(strict_types=1);

/**
 * ContextTest.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-16
 */

namespace UnitTest\Ares\Validation;

use Ares\Schema\Schema;
use Ares\Validation\Context;
use Ares\Validation\Error\Error;
use Ares\Validation\Error\ErrorMessageRenderer;
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
        $errorMessageRenderer = new ErrorMessageRenderer();
        $context = new Context($data, $errorMessageRenderer);

        $this->assertSame($data, $context->getData());
    }

    /**
     * @covers ::__construct
     * @covers ::getErrorMessageRenderer
     *
     * @return void
     */
    public function testGetErrorMessageRendererAccessors(): void
    {
        $data = ['foo' => 'bar'];
        $errorMessageRenderer = new ErrorMessageRenderer();
        $context = new Context($data, $errorMessageRenderer);

        $this->assertSame($errorMessageRenderer, $context->getErrorMessageRenderer());
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
        $data = ['foo' => 'bar'];
        $errorMessageRenderer = new ErrorMessageRenderer();
        $context = new Context($data, $errorMessageRenderer);
        $schema = new Schema();

        $this->assertFalse($context->hasErrors());

        $context->enter('foo', $schema);

        $this->assertSame($context, $context->addError('rule_id', 'message'));
        $this->assertTrue($context->hasErrors());
        $this->assertEquals([new Error(['foo'], 'rule_id', 'message')], $context->getErrors());

        $context->enter('bar', $schema);

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
        $data = ['foo' => 'bar'];
        $errorMessageRenderer = new ErrorMessageRenderer();
        $context = new Context($data, $errorMessageRenderer);

        $schema1 = new Schema();
        $schema2 = new Schema();

        $this->assertEquals([], $context->getSource());
        $this->assertSame($context, $context->enter('foo', $schema1));
        $this->assertEquals(['foo'], $context->getSource());
        $this->assertEquals($schema1, $context->getSchema());
        $this->assertSame($context, $context->enter('bar', $schema2));
        $this->assertEquals(['foo', 'bar'], $context->getSource());
        $this->assertEquals($schema2, $context->getSchema());
        $this->assertSame($context, $context->leave());
        $this->assertEquals(['foo'], $context->getSource());
        $this->assertEquals($schema1, $context->getSchema());
        $this->assertSame($context, $context->leave());
        $this->assertEquals([], $context->getSource());
    }
}

