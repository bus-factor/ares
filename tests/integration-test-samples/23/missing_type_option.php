<?php

declare(strict_types=1);

/**
 * missing_type_option.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-09
 */

use Ares\Ares;
use Ares\Exception\InvalidValidationSchemaException;

$schema = [];

$this->expectException(InvalidValidationSchemaException::class);
$this->expectExceptionMessage('Insufficient validation schema:  contains no `type` validation rule');

$ares = new Ares($schema);

