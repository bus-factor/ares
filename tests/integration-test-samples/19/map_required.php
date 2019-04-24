<?php

declare(strict_types=1);

/**
 * map_required.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-09
 */

use Ares\Validation\Error\Error;
use Ares\Validation\Validator;

$schema = [
    'type' => 'map',
    'schema' => [],
];

$data = null;

$expectedErrors = [
    new Error([''], 'nullable', 'Value must not be null'),
];

$validator = new Validator($schema);

$this->assertSame(empty($expectedErrors), $validator->validate($data));
$this->assertEquals($expectedErrors, $validator->getErrors());

