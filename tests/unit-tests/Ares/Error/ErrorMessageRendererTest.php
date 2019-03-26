<?php

declare(strict_types=1);

/**
 * ErrorMessageRendererTest.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-26
 */

namespace UnitTest\Ares\Error;

use Ares\Context;
use Ares\Error\ErrorMessageRenderer;
use PHPUnit\Framework\TestCase;

/**
 * Class ErrorMessageRendererTest
 *
 * @coversDefaultClass \Ares\Error\ErrorMessageRenderer
 */
class ErrorMessageRendererTest extends TestCase
{
    /**
     * @covers ::render
     *
     * @testWith ["Test", {}, "Test"]
     *           ["Test {foo}", {"foo": "YAY"}, "Test YAY"]
     *           ["Test {foo} {foo}", {"foo": "YAY"}, "Test YAY YAY"]
     *
     * @param string $message        Message format.
     * @param array  $substitutions  String variable substitutions.
     * @param string $expectedRetVal Expected return value.
     * @return void
     */
    public function testRender(string $message, array $substitutions, string $expectedRetVal): void
    {
        $data = [];
        $errorMessageRenderer = new ErrorMessageRenderer();
        $context = new Context($data, $errorMessageRenderer);
        $ruleId = 'foobar';

        $this->assertSame($expectedRetVal, $errorMessageRenderer->render($context, $ruleId, $message, $substitutions));
    }
}

