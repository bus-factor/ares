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
use PHPUnit\Framework\TestCase;

/**
 * Class RequiredRuleTest
 *
 * @coversDefaultClass \Ares\Rule\RequiredRule
 */
class RequiredRuleTest extends TestCase
{
    /**
     * @covers ::validate
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

        $requiredRule->validate($args, $data, $context);
    }

    /**
     * @covers ::validate
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
            $context->enter($reference, []);
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

        $this->assertSame($expectedRetVal, $requiredRule->validate($args, $data, $context));
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
}

