<?php

declare(strict_types=1);

/**
 * valid_too_small.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-30
 */

use Ares\Ares;
use Ares\Validation\Error\Error;

$schema = [
    'type' => 'integer',
    'min' => 42,
];

$data = 41;

$expectedErrors = [
    new Error([''], 'min', 'Value must not be smaller than 42'),
];

$ares = new Ares($schema);

$this->assertSame(empty($expectedErrors), $ares->validate($data));
$this->assertEquals($expectedErrors, $ares->getValidationErrors()->getArrayCopy());

