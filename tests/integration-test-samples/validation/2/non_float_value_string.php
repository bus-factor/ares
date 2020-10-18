<?php

declare(strict_types=1);

/**
 * non_float_value_string.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-07
 */

use Ares\Ares;
use Ares\Validation\Error\Error;

$schema = ['type' => 'float'];
$data = 'foobar';

$expectedErrors = [
    new Error([''], 'type', 'Invalid type'),
];

$ares = new Ares($schema);

$this->assertSame(empty($expectedErrors), $ares->validate($data));
$this->assertEquals($expectedErrors, $ares->getValidationErrors()->getArrayCopy());

