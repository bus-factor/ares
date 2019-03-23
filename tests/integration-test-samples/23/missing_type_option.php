<?php

declare(strict_types=1);

/**
 * missing_type_option.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-09
 */

use Ares\Exception\InvalidValidationSchemaException;
use Ares\Validator;

$schema = [];

$this->expectException(InvalidValidationSchemaException::class);
$this->expectExceptionMessage('Missing schema option: $schema[\'type\']');

$validator = new Validator($schema);

