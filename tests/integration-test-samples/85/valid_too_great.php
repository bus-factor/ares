<?php

declare(strict_types=1);

/**
 * valid_too_great.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-30
 */

use Ares\Validation\Error\Error;
use Ares\Validation\Validator;

$schema = [
    'type' => 'integer',
    'max' => 42,
];

$data = 43;

$expectedErrors = [
    new Error([''], 'max', 'Value must not be greater than 42'),
];

$validator = new Validator($schema);

$this->assertSame(empty($expectedErrors), $validator->validate($data));
$this->assertEquals($expectedErrors, $validator->getErrors());

