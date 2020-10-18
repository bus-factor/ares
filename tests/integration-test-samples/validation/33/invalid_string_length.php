<?php

declare(strict_types=1);

/**
 * valid_string_length.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-23
 */

use Ares\Ares;
use Ares\Validation\Error\Error;

$schema = [
    'type' => 'string',
    'minlength' => 5,
];

$data = 'foo';

$expectedErrors = [
    new Error([''], 'minlength', 'Value too short'),
];

$ares = new Ares($schema);

$this->assertSame(empty($expectedErrors), $ares->validate($data));
$this->assertEquals($expectedErrors, $ares->getValidationErrors()->getArrayCopy());

