<?php

declare(strict_types=1);

/**
 * map_type_item_list.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-31
 */

use Ares\Ares;
use Ares\Validation\Error\Error;

$schema = [
    'type' => 'list',
    'schema' => [
        'type' => 'map',
        'schema' => [
            'name' => [
                'type' => 'string',
                'required' => true,
            ],
            'email' => [
                'type' => 'string',
                'required' => true,
                'email' => true,
            ],
        ],
    ],
];

$data = [
    [
        'name' => 'John Doe',
    ],
    [
        'email' => 'jane.doe@example.com',
    ],
];

$expectedErrors = [
    new Error(['', '0', 'email'], 'required', 'Value required'),
    new Error(['', '1', 'name'], 'required', 'Value required'),
];

$ares = new Ares($schema);

$this->assertSame(empty($expectedErrors), $ares->validate($data));
$this->assertEquals($expectedErrors, $ares->getValidationErrors());

