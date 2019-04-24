<?php

declare(strict_types=1);

/**
 * incomplete_tuple.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-19
 */

use Ares\Validation\Error\Error;
use Ares\Validation\Validator;

$schema = [
    'type' => 'tuple',
    'schema' => [
        ['type' => 'integer'],
        ['type' => 'string'],
    ],
];

$data = [1];

$expectedErrors = [
    new Error(['', 1], 'required', 'Value required'),
];

$validator = new Validator($schema);

$this->assertSame(empty($expectedErrors), $validator->validate($data));
$this->assertEquals($expectedErrors, $validator->getErrors());

