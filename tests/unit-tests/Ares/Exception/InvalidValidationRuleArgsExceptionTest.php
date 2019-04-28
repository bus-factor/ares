<?php

declare(strict_types=1);

/**
 * InvalidValidationRuleArgsExceptionTest.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-30
 */

namespace UnitTest\Ares\Exception;

use Ares\Exception\InvalidValidationRuleArgsException;
use LogicException;
use PHPUnit\Framework\TestCase;

/**
 * Class InvalidValidationRuleArgsExceptionTest
 *
 * @coversDefaultClass \Ares\Exception\InvalidValidationRuleArgsException
 */
class InvalidValidationRuleArgsExceptionTest extends TestCase
{
    /**
     * @return void
     */
    public function testInstanceOf(): void
    {
        $invalidValidationRuleArgsException = new InvalidValidationRuleArgsException();

        $this->assertInstanceOf(LogicException::class, $invalidValidationRuleArgsException);
    }
}

