<?php

declare(strict_types=1);

/**
 * valid_string_length.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-23
 */

use Ares\Validation\Error;
use Ares\Validation\Validator;

$schema = [
    'type' => 'string',
    'maxlength' => 5,
];

$data = 'John Doe';

$expectedErrors = [
    new Error([''], 'maxlength', 'Value too long'),
];

$validator = new Validator($schema);

$this->assertSame(empty($expectedErrors), $validator->validate($data));
$this->assertEquals($expectedErrors, $validator->getErrors());

