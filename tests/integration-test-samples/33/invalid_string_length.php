<?php

declare(strict_types=1);

/**
 * valid_string_length.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-23
 */

use Ares\Error\Error;
use Ares\Validator;

$schema = [
    'type' => 'string',
    'minlength' => 5,
];

$data = 'foo';

$expectedErrors = [
    new Error([''], 'minlength', 'Value too short'),
];

$validator = new Validator($schema);

$this->assertSame(empty($expectedErrors), $validator->validate($data));
$this->assertEquals($expectedErrors, $validator->getErrors());

