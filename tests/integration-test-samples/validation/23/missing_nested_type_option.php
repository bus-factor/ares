<?php

declare(strict_types=1);

/**
 * missing_nested_type_option.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-09
 */

use Ares\Ares;
use Ares\Exception\InvalidSchemaException;

$schema = [
    'type' => 'map',
    'schema' => [
        'name' => [],
    ],
];

$this->expectException(InvalidSchemaException::class);
$this->expectExceptionMessage('Insufficient schema: /schema/name contains no `type` validation rule');

$ares = new Ares($schema);

