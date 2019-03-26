<?php

declare(strict_types=1);

/**
 * forbidden_value_other_syntax.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-26
 */

use Ares\Error\Error;
use Ares\Validator;

$schema = [
    'type' => 'string',
    'allowed' => [
        'values' => ['foo', 'bar'],
    ],
];

$data = 'fizz';

$expectedErrors = [
    new Error([''], 'allowed', 'Value not allowed'),
];

$validator = new Validator($schema);

$this->assertSame(empty($expectedErrors), $validator->validate($data));
$this->assertEquals($expectedErrors, $validator->getErrors());

