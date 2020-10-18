<?php

declare(strict_types=1);

/**
 * non_required_non_blankable_string.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-08
 */

use Ares\Ares;
use Ares\Validation\Error\Error;

$schema = ['type' => 'string', 'blankable' => false];
$data = " \n\t\r ";

$expectedErrors = [
    new Error([''], 'blankable', 'Value must not be blank'),
];

$ares = new Ares($schema);

$this->assertSame(empty($expectedErrors), $ares->validate($data));
$this->assertEquals($expectedErrors, $ares->getValidationErrors()->getArrayCopy());

