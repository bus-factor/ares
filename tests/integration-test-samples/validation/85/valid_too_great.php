<?php

declare(strict_types=1);

/**
 * valid_too_great.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-30
 */

use Ares\Ares;
use Ares\Validation\Error\Error;

$schema = [
    'type' => 'integer',
    'max' => 42,
];

$data = 43;

$expectedErrors = [
    new Error([''], 'max', 'Value must not be greater than 42'),
];

$ares = new Ares($schema);

$this->assertSame(empty($expectedErrors), $ares->validate($data));
$this->assertEquals($expectedErrors, $ares->getValidationErrors()->getArrayCopy());

