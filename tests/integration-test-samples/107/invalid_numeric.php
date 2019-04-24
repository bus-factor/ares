<?php

declare(strict_types=1);

/**
 * invalid_numeric.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-19
 */

use Ares\Validation\Error\Error;
use Ares\Validation\Validator;

$schema = ['type' => 'numeric'];
$data = '13.37';

$expectedErrors = [
    new Error([''], 'type', 'Invalid type'),
];

$validator = new Validator($schema);

$this->assertSame(empty($expectedErrors), $validator->validate($data));
$this->assertEquals($expectedErrors, $validator->getErrors());

