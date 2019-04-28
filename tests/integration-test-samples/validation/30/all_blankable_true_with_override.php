<?php

declare(strict_types=1);

/**
 * all_blankable_true_with_override.php
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
            'blankable' => false,
        ],
        'email' => [
            'type' => 'string',
        ],
    ],
];

$options = [
    'allBlankable' => true,
];

$data = [
    'name' => '',
    'email' => '',
];

$expectedErrors = [
    new Error(['', 'name'], 'blankable', 'Value must not be blank'),
];

$ares = new Ares($schema);

$this->assertSame(empty($expectedErrors), $ares->validate($data, $options));
$this->assertEquals($expectedErrors, $ares->getValidationErrors());

