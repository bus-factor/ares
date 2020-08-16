<?php

declare(strict_types=1);

/**
 * required_false_nullable_true_2.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2020-08-16
 */

use Ares\Ares;

$schema = [
    'type' => 'map',
    'schema' => [
        'date' => [
            'type' => 'string',
            'required' => false,
            'nullable' => true,
            'datetime' => 'Y-m-d',
        ],
    ],
];

$data = ['date' => null];

$expectedErrors = [];

$ares = new Ares($schema);

$this->assertSame(empty($expectedErrors), $ares->validate($data));
$this->assertEquals($expectedErrors, $ares->getValidationErrors());

