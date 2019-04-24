<?php

declare(strict_types=1);

/**
 * forbidden_value.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-21
 */

use Ares\Validation\Error\Error;
use Ares\Validation\Validator;

$schema = [
    'type' => 'string',
    'allowed' => ['foo', 'bar'],
];

$data = 'fizz';

$expectedErrors = [
    new Error([''], 'allowed', 'Value not allowed'),
];

$validator = new Validator($schema);

$this->assertSame(empty($expectedErrors), $validator->validate($data));
$this->assertEquals($expectedErrors, $validator->getErrors());

