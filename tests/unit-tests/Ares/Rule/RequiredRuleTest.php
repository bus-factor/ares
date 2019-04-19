<?php

declare(strict_types=1);

/**
 * RequiredRuleTest.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-21
 */

namespace UnitTest\Ares\Rule;

use Ares\Context;
use Ares\Error\ErrorMessageRenderer;
use Ares\Exception\InvalidValidationRuleArgsException;
use Ares\Rule\RequiredRule;
use Ares\Rule\TypeRule;
use Ares\Schema\Rule;
use Ares\Schema\Schema;
use Ares\Schema\SchemaMap;
use PHPUnit\Framework\TestCase;

/**
 * Class RequiredRuleTest
 *
 * @coversDefaultClass \Ares\Rule\RequiredRule
 */
class RequiredRuleTest extends TestCase
{
    /**
     * @testWith ["Ares\\Rule\\RuleInterface"]
     *           ["Ares\\Rule\\AbstractRule"]
     *
     * @param string $fqcn Fully-qualified class name of the interface or class.
     * @return void
     */
    public function testInstanceOf(string $fqcn): void
    {
        $requiredRule = new RequiredRule();

        $this->assertInstanceOf($fqcn, $requiredRule);
    }

    /**
     * @covers ::getSupportedTypes
     *
     * @testWith ["boolean"]
     *           ["float"]
     *           ["integer"]
     *           ["list"]
     *           ["map"]
     *           ["string"]
     *           ["tuple"]
     *
     * @param string $type Supported type.
     * @return void
     */
    public function testGetSupportedTypes(string $type): void
    {
        $requiredRule = new RequiredRule();

        $this->assertContains($type, $requiredRule->getSupportedTypes());
    }

    /**
     * @covers ::isApplicable
     *
     * @return void
     */
    public function testIsApplicable(): void
    {
        $data = [];
        $requiredRule = new RequiredRule();
        $context = new Context($data, new ErrorMessageRenderer());

        $this->assertTrue($requiredRule->isApplicable($context));
    }

    /**
     * @covers ::performValidation
     *
     * @testWith [1]
     *           [17.2]
     *           [null]
     *           ["foo"]
     *
     * @param mixed $args Validation rule configuration.
     * @return void
     */
    public function testValidateToHandleInvalidValidationRuleArgs($args): void
    {
        $data = 'foo';
        $context = new Context($data, new ErrorMessageRenderer());
        $requiredRule = new RequiredRule();

        $this->expectException(InvalidValidationRuleArgsException::class);
        $this->expectExceptionMessage('Invalid args: ' . json_encode($args));

        $requiredRule->performValidation($args, $data, $context);
    }

    /**
     * @covers ::performValidation
     *
     * @dataProvider getValidateSamples
     *
     * @param bool|array  $args               Validation rule configuration.
     * @param mixed       $data               Validated data.
     * @param array       $source             Source references.
     * @param bool        $expectedRetVal     Expected validation return value.
     * @param bool        $expectError        Indicates if an error is expected.
     * @param string      $expectErrorMessage Expected error message string.
     * @return void
     */
    public function testValidate(
        $args,
        $data,
        array $source,
        bool $expectedRetVal,
        bool $expectError,
        string $expectErrorMessage = 'Value required'
    ): void {
        $context = $this->getMockBuilder(Context::class)
            ->setConstructorArgs([&$data, new ErrorMessageRenderer()])
            ->setMethods(['addError', 'getMessage'])
            ->getMock();

        foreach ($source as $reference) {
            $context->enter($reference, (new Schema())->setRule(new Rule(RequiredRule::ID, $args)));
        }

        if ($expectError) {
            $context->expects($this->once())
                ->method('addError')
                ->with(RequiredRule::ID, $expectErrorMessage);
        } else {
            $context->expects($this->never())
                ->method('addError');
        }

        $requiredRule = new RequiredRule();

        $this->assertSame($expectedRetVal, $requiredRule->performValidation($args, $data, $context));
    }

    /**
     * @return array
     */
    public function getValidateSamples(): array
    {
        return [
            'primitive value' => [
                true,
                'foobar',
                [''],
                true,
                false,
            ],
            'not required + absent' => [
                false,
                [],
                ['', 'name'],
                false,
                false,
            ],
            'not required + present' => [
                false,
                ['name' => 'John Doe'],
                ['', 'name'],
                true,
                false,
            ],
            'required + absent' => [
                true,
                [],
                ['', 'name'],
                false,
                true,
            ],
            'required + present' => [
                true,
                ['name' => 'John Doe'],
                ['', 'name'],
                true,
                false,
            ],
            'not required + absent 2' => [
                false,
                ['account' => []],
                ['', 'account', 'name'],
                false,
                false,
            ],
            'not required + present 2' => [
                false,
                ['account' => ['name' => 'John Doe']],
                ['', 'account', 'name'],
                true,
                false,
            ],
            'required + absent 2' => [
                true,
                ['account' => []],
                ['', 'account', 'name'],
                false,
                true,
            ],
            'required + present 2' => [
                true,
                ['account' => ['name' => 'John Doe']],
                ['', 'account', 'name'],
                true,
                false,
            ],
        ];
    }

    /**
     * @covers ::performValidation
     *
     * @return void
     */
    public function testValidateHandlesCustomMessage(): void
    {
        $args = true;
        $data = [];
        $customMessage = 'Please do not forget to provide this field';

        $context = $this->getMockBuilder(Context::class)
            ->setConstructorArgs([&$data, new ErrorMessageRenderer()])
            ->setMethods(['addError', 'getMessage'])
            ->getMock();

        $innerSchema = (new Schema())
            ->setRule(new Rule(TypeRule::ID, 'string'))
            ->setRule(new Rule(RequiredRule::ID, true, $customMessage));

        $context->enter('', (new SchemaMap())->setSchemas(['foo' => $innerSchema]));
        $context->enter('foo', $innerSchema);

        $context->expects($this->once())
            ->method('addError')
            ->with(RequiredRule::ID, $customMessage);

        $requiredRule = new RequiredRule();

        $this->assertFalse($requiredRule->performValidation($args, $data, $context));
    }
}

