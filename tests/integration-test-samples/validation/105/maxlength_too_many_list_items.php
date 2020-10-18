<?php

declare(strict_types=1);

/**
 * maxlength_too_many_list_items.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-20
 */

use Ares\Ares;
use Ares\Validation\Error\Error;

$schema = [
    'type' => 'list',
    'maxlength' => 3,
    'schema' => [
        'type' => 'integer',
    ],
];

$data = [1, 2, 3, 4];

$expectedErrors = [
    new Error([''], 'maxlength', 'Too many items'),
];

$ares = new Ares($schema);

$this->assertSame(empty($expectedErrors), $ares->validate($data));
$this->assertEquals($expectedErrors, $ares->getValidationErrors()->getArrayCopy());

