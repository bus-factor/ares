<?php

declare(strict_types=1);

/**
 * custom_required_error_message.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-26
 */

use Ares\Error\Error;
use Ares\Validator;

$schema = [
    'type' => 'map',
    'schema' => [
        'name' => [
            'type' => 'string',
            'required' => [
                'message' => 'The field <{field}> is required',
            ],
        ],
    ],
];

$data = [];

$expectedErrors = [
    new Error(['', 'name'], 'required', 'The field <name> is required'),
];

$validator = new Validator($schema);

$this->assertSame(empty($expectedErrors), $validator->validate($data));
$this->assertEquals($expectedErrors, $validator->getErrors());

