<?php

declare(strict_types=1);

/**
 * required_true_nullable_true.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-13
 */

use Ares\Validator;

$schema = [
    'type' => 'map',
    'schema' => [
        'name' => [
            'type' => 'string',
            'required' => true,
            'nullable' => true,
        ],
    ],
];

$data = ['name' => null];

$expectedErrors = [];

$validator = new Validator($schema);

$this->assertSame(empty($expectedErrors), $validator->validate($data));
$this->assertEquals($expectedErrors, $validator->getErrors());

