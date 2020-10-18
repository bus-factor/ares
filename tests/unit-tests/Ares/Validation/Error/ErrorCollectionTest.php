<?php

/**
 * ErrorCollectionTest.php
 *
 * Author: Michael Leßnau <michael.lessnau@gmail.com>
 * Date:   2020-10-18
 */

declare(strict_types=1);

namespace UnitTest\Ares\Validation\Error;

use Ares\Validation\Error\Error;
use Ares\Validation\Error\ErrorCollection;
use PHPUnit\Framework\TestCase;

/**
 * Class ErrorCollectionTest
 *
 * @coversDefaultClass \Ares\Validation\Error\ErrorCollection
 * @uses \Ares\Validation\Error\Error
 */
class ErrorCollectionTest extends TestCase
{
    /**
     * @covers ::toArrayJsonApiStyle
     *
     * @return void
     */
    public function testToArrayJsonApiStyle(): void
    {
        $errors = [
            new Error(['', 'data', 'email'], 'empty', 'Value must not be empty'),
            new Error(['', 'data', 'password'], 'nullable', 'Value must not be null', ['type' => 'warning']),
        ];

        $errorCollection = new ErrorCollection($errors);

        $retVal = $errorCollection->toArrayJsonApiStyle();

        $expectedRetVal = [
            [
                'code' => 'empty',
                'status' => '422',
                'title' => 'Unprocessable Entity',
                'detail' => 'Value must not be empty',
                'source' => [
                    'pointer' => '/data/email',
                ],
                'meta' => [],
            ],
            [
                'code' => 'nullable',
                'status' => '422',
                'title' => 'Unprocessable Entity',
                'detail' => 'Value must not be null',
                'source' => [
                    'pointer' => '/data/password',
                ],
                'meta' => [
                    'type' => 'warning',
                ],
            ],
        ];

        $this->assertEquals($expectedRetVal, $retVal);
    }

    /**
     * @covers ::toArrayNested
     *
     * @return void
     */
    public function testToArrayNested(): void
    {
        $errors = [
            new Error(['', 'data', 'email'], 'empty', 'Value must not be empty'),
            new Error(['', 'data', 'password'], 'nullable', 'Value must not be null', ['type' => 'warning']),
        ];

        $errorCollection = new ErrorCollection($errors);

        $retVal = $errorCollection->toArrayNested();

        $expectedRetVal = [
            'data' => [
                'email' => [
                    'empty' => 'Value must not be empty',
                ],
                'password' => [
                    'nullable' => 'Value must not be null',
                ],
            ],
        ];

        $this->assertEquals($expectedRetVal, $retVal);
    }
}
