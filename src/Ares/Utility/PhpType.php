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
    public const ARRAY   = 'array';
    public const BOOLEAN = 'boolean';
    public const DOUBLE  = 'double';
    public const INTEGER = 'integer';
    public const NULL    = 'NULL';
    public const STRING  = 'string';
}

