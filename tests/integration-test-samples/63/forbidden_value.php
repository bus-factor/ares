<?php

declare(strict_types=1);

/**
 * forbidden_value.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-22
 */

use Ares\Ares;
use Ares\Validation\Error\Error;

$schema = [
    'type' => 'string',
    'forbidden' => ['foo', 'bar'],
];

$data = 'foo';

$expectedErrors = [
    new Error([''], 'forbidden', 'Value forbidden'),
];

$ares = new Ares($schema);

$this->assertSame(empty($expectedErrors), $ares->validate($data));
$this->assertEquals($expectedErrors, $ares->getValidationErrors());

