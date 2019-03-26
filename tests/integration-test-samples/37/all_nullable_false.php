<?php

declare(strict_types=1);

/**
 * all_nullable_false.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-15
 */

use Ares\Error\Error;
use Ares\Validator;

$schema = [
    'type' => 'map',
    'schema' => [
        'name' => [
            'type' => 'string',
        ],
    ],
];

$options = [
    'allNullable' => false,
];

$data = ['name' => null];

$expectedErrors = [
    new Error(['', 'name'], 'nullable', 'Value must not be null'),
];

$validator = new Validator($schema, $options);

$this->assertSame(empty($expectedErrors), $validator->validate($data));
$this->assertEquals($expectedErrors, $validator->getErrors());

