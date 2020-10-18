<?php

declare(strict_types=1);

/**
 * incomplete_tuple.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-19
 */

use Ares\Ares;
use Ares\Validation\Error\Error;

$schema = [
    'type' => 'tuple',
    'schema' => [
        ['type' => 'integer'],
        ['type' => 'string'],
    ],
];

$data = [1];

$expectedErrors = [
    new Error(['', 1], 'required', 'Value required'),
];

$ares = new Ares($schema);

$this->assertSame(empty($expectedErrors), $ares->validate($data));
$this->assertEquals($expectedErrors, $ares->getValidationErrors()->getArrayCopy());

