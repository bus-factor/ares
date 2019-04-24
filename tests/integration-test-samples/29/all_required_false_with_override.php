<?php

declare(strict_types=1);

/**
 * all_required_false_with_override.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-13
 */

use Ares\Ares;
use Ares\Validation\Error\Error;

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

$ares = new Ares($schema);

$this->assertSame(empty($expectedErrors), $ares->validate($data, $options));
$this->assertEquals($expectedErrors, $ares->getValidationErrors());

