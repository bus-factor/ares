<?php

declare(strict_types=1);

/**
 * all_nullable_true.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-15
 */

use Ares\Validation\Validator;

$schema = [
    'type' => 'map',
    'schema' => [
        'name' => [
            'type' => 'string',
        ],
    ],
];

$options = [
    'allNullable' => true,
];

$data = ['name' => null];

$expectedErrors = [];

$validator = new Validator($schema, $options);

$this->assertSame(empty($expectedErrors), $validator->validate($data));
$this->assertEquals($expectedErrors, $validator->getErrors());
