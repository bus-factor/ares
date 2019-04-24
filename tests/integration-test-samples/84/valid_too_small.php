<?php

declare(strict_types=1);

/**
 * valid_too_small.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-30
 */

use Ares\Validation\Error\Error;
use Ares\Validation\Validator;

$schema = [
    'type' => 'integer',
    'min' => 42,
];

$data = 41;

$expectedErrors = [
    new Error([''], 'min', 'Value must not be smaller than 42'),
];

$validator = new Validator($schema);

$this->assertSame(empty($expectedErrors), $validator->validate($data));
$this->assertEquals($expectedErrors, $validator->getErrors());

