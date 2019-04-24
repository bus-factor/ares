<?php

declare(strict_types=1);

/**
 * map_without_schema.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-09
 */

use Ares\Ares;
use Ares\Exception\InvalidValidationSchemaException;

$schema = [
    'type' => 'map',
];

$this->expectException(InvalidValidationSchemaException::class);
$this->expectExceptionMessage('Missing validation schema key:  uses type "map" but contains no "schema" key');

$ares = new Ares($schema);

