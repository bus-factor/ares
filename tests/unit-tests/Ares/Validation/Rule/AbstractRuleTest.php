<?php

declare(strict_types=1);

/**
 * AbstractRuleTest.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-18
 */

namespace UnitTest\Ares\Validation\Rule;

use Ares\Exception\InapplicableValidationRuleException;
use Ares\Schema\Rule;
use Ares\Schema\Schema;
use Ares\Validation\Context;
use Ares\Validation\Error\ErrorMessageRenderer;
use Ares\Validation\Rule\AbstractRule;
use Ares\Validation\Rule\TypeRule;
use PHPUnit\Framework\TestCase;

/**
 * Class AbstractRuleTest
 *
 * @coversDefaultClass \Ares\Validation\Rule\AbstractRule
 */
class AbstractRuleTest extends TestCase
{
    /**
     * @covers \Ares\Validation\Rule\AbstractRule
     * @testWith ["Ares\\Validation\\Rule\\RuleInterface"]
     *
     * @param string $fqcn Fully-qualified class name of the interface, or class.
     * @return void
     */
    public function testInstanceOf(string $fqcn): void
    {
        $abstractRule = $this->getMockBuilder(AbstractRule::class)
            ->getMockForAbstractClass();

        $this->assertInstanceOf($fqcn, $abstractRule);
    }

    /**
     * @covers ::isApplicable
     *
     * @testWith ["integer", ["integer", "float"], true]
     *           ["integer", [], false]
     *           ["integer", ["float"], false]
     *
     * @param string $type           Schema type.
     * @param array  $supportedTypes Supported schema types.
     * @param bool   $expectedRetVal Expected return value.
     * @return void
     */
    public function testIsApplicable(string $type, array $supportedTypes, bool $expectedRetVal): void
    {
        $schema = new Schema();
        $schema->setRule(new Rule(TypeRule::ID, $type));

        $data = '';

        $context = new Context($data, new ErrorMessageRenderer());
        $context->enter('', $schema);

        $abstractRule = $this->getMockBuilder(AbstractRule::class)
            ->onlyMethods(['getSupportedTypes'])
            ->getMockForAbstractClass();

        $abstractRule->expects($this->once())
            ->method('getSupportedTypes')
            ->willReturn($supportedTypes);

        $this->assertSame($expectedRetVal, $abstractRule->isApplicable($context));
    }

    /**
     * @covers ::validate
     *
     * @testWith [true]
     *           [false]
     *
     * @param bool $retVal Return value.
     * @return void
     */
    public function testValidate(bool $retVal): void
    {
        $args = true;
        $data = '';

        $context = new Context($data, new ErrorMessageRenderer());

        $abstractRule = $this->getMockBuilder(AbstractRule::class)
            ->onlyMethods(['isApplicable', 'performValidation'])
            ->getMockForAbstractClass();

        $abstractRule->expects($this->once())
            ->method('isApplicable')
            ->willReturn(true);

        $abstractRule->expects($this->once())
            ->method('performValidation')
            ->with($args, $data, $context)
            ->willReturn($retVal);

        $this->assertSame($retVal, $abstractRule->validate($args, $data, $context));
    }

    /**
     * @covers ::validate
     *
     * @return void
     */
    public function testValidateHandlesRuleInapplicability(): void
    {
        $args = true;
        $data = '';

        $context = new Context($data, new ErrorMessageRenderer());

        $abstractRule = $this->getMockBuilder(AbstractRule::class)
            ->onlyMethods(['getSupportedTypes', 'isApplicable', 'performValidation'])
            ->getMockForAbstractClass();

        $abstractRule->expects($this->once())
            ->method('getSupportedTypes')
            ->willReturn(['string', 'map']);

        $abstractRule->expects($this->once())
            ->method('isApplicable')
            ->willReturn(false);

        $abstractRule->expects($this->never())
            ->method('performValidation');

        $this->expectException(InapplicableValidationRuleException::class);
        $this->expectExceptionMessage('Rule not applicable. Allowed types: <string>, <map>');

        $abstractRule->validate($args, $data, $context);
    }
}

