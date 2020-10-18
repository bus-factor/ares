<?php

declare(strict_types=1);

/**
 * forbidden_value.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-21
 */

use Ares\Ares;
use Ares\Validation\Error\Error;

$schema = [
    'type' => 'string',
    'allowed' => ['foo', 'bar'],
];

$data = 'fizz';

$expectedErrors = [
    new Error([''], 'allowed', 'Value not allowed'),
];

$ares = new Ares($schema);

$this->assertSame(empty($expectedErrors), $ares->validate($data));
$this->assertEquals($expectedErrors, $ares->getValidationErrors()->getArrayCopy());

