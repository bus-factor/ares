<?php

declare(strict_types=1);

/**
 * map_without_schema.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-09
 */

use Ares\Exception\InvalidValidationSchemaException;
use Ares\Validation\Validator;

$schema = [
    'type' => 'map',
    'required' => true,
];

$data = [];

$validator = new Validator($schema);

$this->expectException(InvalidValidationSchemaException::class);
$this->expectExceptionMessage('Missing schema option: $schema[\'schema\']');

$validator->validate($data);
