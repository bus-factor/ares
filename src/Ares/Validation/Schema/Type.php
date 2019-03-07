<?php

declare(strict_types=1);

/**
 * Type.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-07
 */

namespace Ares\Validation\Schema;

use Ares\Utility\Enum;

/**
 * Class Type
 */
class Type extends Enum
{
    // simple
    const BOOLEAN = 'boolean';
    const FLOAT   = 'float';
    const INTEGER = 'integer';
    const STRING  = 'string';
}

