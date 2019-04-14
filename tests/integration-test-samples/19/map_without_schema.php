<?php

declare(strict_types=1);

/**
 * map_without_schema.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-09
 */

use Ares\Exception\InvalidValidationSchemaException;
use Ares\Validator;

$schema = [
    'type' => 'map',
    'required' => true,
];

$this->expectException(InvalidValidationSchemaException::class);
$this->expectExceptionMessage('Missing validation schema key:  uses type "map" but contains no "schema" key');

$validator = new Validator($schema);

