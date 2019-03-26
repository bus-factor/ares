<?php

declare(strict_types=1);

/**
 * custom_blankable_error_message.php
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
            'blankable' => ['message' => 'The field <{field}> must not be blank']
        ],
    ],
];

$data = ['name' => ''];

$expectedErrors = [
    new Error(['', 'name'], 'blankable', 'The field <name> must not be blank'),
];

$validator = new Validator($schema);

$this->assertSame(empty($expectedErrors), $validator->validate($data));
$this->assertEquals($expectedErrors, $validator->getErrors());

