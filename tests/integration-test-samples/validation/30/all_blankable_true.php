<?php

declare(strict_types=1);

/**
 * all_blankable_true.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-13
 */

use Ares\Ares;

$schema = [
    'type' => 'map',
    'schema' => [
        'name' => [
            'type' => 'string',
        ],
    ],
];

$options = [
    'allBlankable' => true,
];

$data = ['name' => ''];

$expectedErrors = [];

$ares = new Ares($schema);

$this->assertSame(empty($expectedErrors), $ares->validate($data, $options));
$this->assertEquals($expectedErrors, $ares->getValidationErrors()->getArrayCopy());

