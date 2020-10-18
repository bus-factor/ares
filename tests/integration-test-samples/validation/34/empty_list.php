<?php

declare(strict_types=1);

/**
 * empty_list.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-31
 */

use Ares\Ares;

$schema = [
    'type' => 'list',
    'schema' => [
        'type' => 'integer',
    ],
];

$data = [];

$expectedErrors = [];

$ares = new Ares($schema);

$this->assertSame(empty($expectedErrors), $ares->validate($data));
$this->assertEquals($expectedErrors, $ares->getValidationErrors()->getArrayCopy());

