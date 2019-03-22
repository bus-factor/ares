<?php

declare(strict_types=1);

/**
 * forbidden_value.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-22
 */

use Ares\Validation\Error;
use Ares\Validation\Validator;

$schema = [
    'type' => 'string',
    'forbidden' => ['foo', 'bar'],
];

$data = 'foo';

$expectedErrors = [
    new Error([''], 'forbidden', 'Value forbidden'),
];

$validator = new Validator($schema);

$this->assertSame(empty($expectedErrors), $validator->validate($data));
$this->assertEquals($expectedErrors, $validator->getErrors());

