<?php

declare(strict_types=1);

/**
 * any_date_format_invalid_value.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-23
 */

use Ares\Validation\Error;
use Ares\Validation\Validator;

$schema = [
    'type' => 'string',
    'datetime' => true,
];

$data = '2019-03-2873';

$expectedErrors = [
    new Error([''], 'datetime', 'Invalid date/time value'),
];

$validator = new Validator($schema);

$this->assertSame(empty($expectedErrors), $validator->validate($data));
$this->assertEquals($expectedErrors, $validator->getErrors());

