<?php

declare(strict_types=1);

/**
 * missing_nested_type_option.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-09
 */

use Ares\Exception\InvalidValidationSchemaException;
use Ares\Validator;

$schema = [
    'type' => 'map',
    'schema' => [
        'name' => [],
    ],
];

$this->expectException(InvalidValidationSchemaException::class);
$this->expectExceptionMessage('Insufficient validation schema: /schema/name contains no `type` validation rule');

$validator = new Validator($schema);

