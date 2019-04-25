<?php

declare(strict_types=1);

/**
 * any_date_format_invalid_value.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-23
 */

use Ares\Ares;
use Ares\Validation\Error\Error;

$schema = [
    'type' => 'string',
    'datetime' => true,
];

$data = '2019-03-2873';

$expectedErrors = [
    new Error([''], 'datetime', 'Invalid date/time value'),
];

$ares = new Ares($schema);

$this->assertSame(empty($expectedErrors), $ares->validate($data));
$this->assertEquals($expectedErrors, $ares->getValidationErrors());

