<?php

declare(strict_types=1);

/**
 * non_float_value_boolean.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-07
 */

use Ares\Validation\Error\Error;
use Ares\Validation\Validator;

$schema = ['type' => 'float'];
$data = false;

$expectedErrors = [
    new Error([''], 'type', 'Invalid type'),
];

$validator = new Validator($schema);

$this->assertSame(empty($expectedErrors), $validator->validate($data));
$this->assertEquals($expectedErrors, $validator->getErrors());

