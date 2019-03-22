<?php

declare(strict_types=1);

/**
 * ValidatorTest.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-22
 */

namespace UnitTest\Ares\Validation;

use Ares\Exception\UnknownValidationRuleIdException;
use Ares\Validation\Validator;
use PHPUnit\Framework\TestCase;

/**
 * Class ValidationTest
 *
 * @coversDefaultClass \Ares\Validation\Validator
 */
class ValidationTest extends TestCase
{
    /**
     * @covers ::validate
     * @covers ::performValidation
     * @covers ::getRule
     *
     * @return void
     */
    public function testValidateHandlesUnknownValidationRuleIds(): void
    {
        $schema = ['type' => 'string', 'uargh' => true];
        $validator = new Validator($schema);

        $this->expectException(UnknownValidationRuleIdException::class);
        $this->expectExceptionMessage('Unknown validation rule ID: uargh');

        $validator->validate('foobar');
    }
}

