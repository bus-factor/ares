<?php

declare(strict_types=1);

/**
 * specific_date_format_valid_value.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-23
 */

use Ares\Validator;

$schema = [
    'type' => 'string',
    'datetime' => 'Y-m-d H:i',
];

$data = '2019-03-23 00:12';

$expectedErrors = [];

$validator = new Validator($schema);

$this->assertSame(empty($expectedErrors), $validator->validate($data));
$this->assertEquals($expectedErrors, $validator->getErrors());

