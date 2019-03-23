<?php

declare(strict_types=1);

/**
 * allowed_value.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-21
 */

use Ares\Validator;

$schema = [
    'type' => 'string',
    'allowed' => ['foo', 'bar'],
];

$data = 'foo';

$expectedErrors = [];

$validator = new Validator($schema);

$this->assertSame(empty($expectedErrors), $validator->validate($data));
$this->assertEquals($expectedErrors, $validator->getErrors());

