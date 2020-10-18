<?php

declare(strict_types=1);

/**
 * allowed_value.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-22
 */

use Ares\Ares;

$schema = [
    'type' => 'string',
    'forbidden' => ['foo', 'bar'],
];

$data = 'fizz';

$expectedErrors = [];

$ares = new Ares($schema);

$this->assertSame(empty($expectedErrors), $ares->validate($data));
$this->assertEquals($expectedErrors, $ares->getValidationErrors()->getArrayCopy());

