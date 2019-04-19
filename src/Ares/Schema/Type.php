<?php

declare(strict_types=1);

/**
 * Type.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-07
 */

namespace Ares\Schema;

use Ares\Utility\Enum;

/**
 * Class Type
 */
class Type extends Enum
{
    public const BOOLEAN = 'boolean';
    public const FLOAT   = 'float';
    public const INTEGER = 'integer';
    public const LIST    = 'list';
    public const MAP     = 'map';
    public const NUMERIC = 'numeric';
    public const STRING  = 'string';
    public const TUPLE   = 'tuple';
}

