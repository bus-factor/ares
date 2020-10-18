<?php

declare(strict_types=1);

/**
 * too_many_tuple_items.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-20
 */

use Ares\Ares;
use Ares\Validation\Error\Error;

$schema = [
    'type' => 'tuple',
    'schema' => [
        ['type' => 'string'],
        ['type' => 'integer'],
    ],
];

$data = ['foo', 1337, 'bar'];

$expectedErrors = [
    new Error(['', 2], 'unknownAllowed', 'Unknown field'),
];

$ares = new Ares($schema);

$this->assertSame(empty($expectedErrors), $ares->validate($data));
$this->assertEquals($expectedErrors, $ares->getValidationErrors()->getArrayCopy());

