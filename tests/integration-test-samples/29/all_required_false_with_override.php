<?php

declare(strict_types=1);

/**
 * all_required_false_with_override.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-13
 */

use Ares\Validation\Error\Error;
use Ares\Validation\Validator;

$schema = [
    'type' => 'map',
    'schema' => [
        'name' => [
            'type' => 'string',
            'required' => true,
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
    'allRequired' => false,
];

$data = ['meta' => []];

$expectedErrors = [
    new Error(['', 'name'], 'required', 'Value required'),
];

$validator = new Validator($schema, $options);

$this->assertSame(empty($expectedErrors), $validator->validate($data));
$this->assertEquals($expectedErrors, $validator->getErrors());

