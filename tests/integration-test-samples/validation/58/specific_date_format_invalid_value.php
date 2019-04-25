<?php

declare(strict_types=1);

/**
 * specific_date_format_invalid_value.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-23
 */

use Ares\Ares;
use Ares\Validation\Error\Error;

$schema = [
    'type' => 'string',
    'datetime' => 'Y-m-d H:i',
];

$data = '23.03.2019 00:12';

$expectedErrors = [
    new Error([''], 'datetime', 'Invalid date/time value'),
];

$ares = new Ares($schema);

$this->assertSame(empty($expectedErrors), $ares->validate($data));
$this->assertEquals($expectedErrors, $ares->getValidationErrors());

