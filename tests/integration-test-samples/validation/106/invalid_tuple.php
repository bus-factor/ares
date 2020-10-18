<?php

declare(strict_types=1);

/**
 * invalid_tuple.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-18
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

$data = ['foo', 'bar'];

$expectedErrors = [
    new Error(['', 0], 'type', 'Invalid type'),
];

$ares = new Ares($schema);

$this->assertSame(empty($expectedErrors), $ares->validate($data));
$this->assertEquals($expectedErrors, $ares->getValidationErrors()->getArrayCopy());

