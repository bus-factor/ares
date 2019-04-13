<?php

declare(strict_types=1);

/**
 * PhpType.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-13
 */

namespace Ares\Utility;

/**
 * Class PhpType
 */
class PhpType extends Enum
{
    const ARRAY   = 'array';
    const BOOLEAN = 'boolean';
    const DOUBLE  = 'double';
    const INTEGER = 'integer';
    const NULL    = 'NULL';
    const STRING  = 'string';
}

