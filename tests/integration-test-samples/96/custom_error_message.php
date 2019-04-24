<?php

declare(strict_types=1);

/**
 * custom_error_message.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-06
 */

use Ares\Validation\Error\Error;
use Ares\Validation\Validator;

$schema = [
    'type' => 'map',
    'schema' => [
        'name' => [
            'type' => 'string',
            ['required' => true, 'message' => 'Please enter your name'],
        ],
        'email' => [
            'type' => 'string',
            'required' => true,
            ['email' => true, 'message' => 'Please enter a valid email address']
        ],
    ],
];

$data = [
    'email' => 'sdfkhasdfkasdj',
];

$expectedErrors = [
    new Error(['', 'name'], 'required', 'Please enter your name'),
    new Error(['', 'email'], 'email', 'Please enter a valid email address'),
];

$validator = new Validator($schema);

$this->assertSame(empty($expectedErrors), $validator->validate($data));
$this->assertEquals($expectedErrors, $validator->getErrors());

