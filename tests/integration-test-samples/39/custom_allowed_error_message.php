<?php

declare(strict_types=1);

/**
 * custom_allowed_error_message.php
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
            'allowed' => [
                'values' => ['small', 'medium'],
                'message' => 'The field <{field}> must contain one of these values: {values}.'
            ],
        ],
    ],
];

$data = ['name' => 'large'];

$expectedErrors = [
    new Error(['', 'name'], 'allowed', 'The field <name> must contain one of these values: "small", "medium".'),
];

$validator = new Validator($schema);

$this->assertSame(empty($expectedErrors), $validator->validate($data));
$this->assertEquals($expectedErrors, $validator->getErrors());

