<?php

declare(strict_types=1);

/**
 * all_blankable_false_with_override.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-13
 */

use Ares\Error\Error;
use Ares\Validator;

$schema = [
    'type' => 'map',
    'schema' => [
        'name' => [
            'type' => 'string',
        ],
        'email' => [
            'type' => 'string',
            'blankable' => true,
        ],
    ],
];

$options = [
    'allBlankable' => false,
];

$data = ['name' => '', 'email' => ''];

$expectedErrors = [
    new Error(['', 'name'], 'blankable', 'Value must not be blank'),
];

$validator = new Validator($schema, $options);

$this->assertSame(empty($expectedErrors), $validator->validate($data));
$this->assertEquals($expectedErrors, $validator->getErrors());

