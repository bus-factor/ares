<?php

declare(strict_types=1);

/**
 * PhpType.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-08
 */

namespace Ares\Validation\Schema;

use Ares\Utility\Enum;

/**
 * Class PhpType
 */
class PhpType extends Enum
{
    const BOOLEAN         = 'boolean';
    const INTEGER         = 'integer';
    const DOUBLE          = 'double';
    const STRING          = 'string';
    const ARRAY           = 'array';
    const OBJECT          = 'object';
    const RESOURCE        = 'resource';
    const RESOURCE_CLOSED = 'resource (closed)';
    const NULL            = 'NULL';
    const UNKNOWN         = 'unknown type';
}

