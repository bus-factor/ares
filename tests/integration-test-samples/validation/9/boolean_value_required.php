<?php

declare(strict_types=1);

/**
 * boolean_value_required.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-07
 */

use Ares\Ares;
use Ares\Validation\Error\Error;

$schema = ['type' => 'boolean'];
$data = null;

$expectedErrors = [
    new Error([''], 'nullable', 'Value must not be null'),
];

$ares = new Ares($schema);

$this->assertSame(empty($expectedErrors), $ares->validate($data));
$this->assertEquals($expectedErrors, $ares->getValidationErrors()->getArrayCopy());

