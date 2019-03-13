<?php

declare(strict_types=1);

/**
 * all_required_true.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-13
 */

use Ares\Validation\Error;
use Ares\Validation\Validator;

$schema = [
    'type' => 'map',
    'schema' => [
        'name' => [
            'type' => 'string',
        ],
        'meta' => [
            'type' => 'map',
            'schema' => [
                'age' => ['type' => 'integer'],
            ],
        ],
    ],
];

$options = [
    'allRequired' => true,
];

$data = ['meta' => []];

$expectedErrors = [
    new Error(['', 'name'], 'required', 'Value required'),
    new Error(['', 'meta', 'age'], 'required', 'Value required'),
];

$validator = new Validator($schema, $options);

$this->assertSame(empty($expectedErrors), $validator->validate($data));
$this->assertEquals($expectedErrors, $validator->getErrors());

