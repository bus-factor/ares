<?php

declare(strict_types=1);

/**
 * ValueType.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-07
 */

namespace Ares\Validation\Schema;

use Ares\Utility\Enum;

/**
 * Class ValueType
 */
class ValueType extends Enum
{
    // simple
    const BOOLEAN = 'boolean';
    const FLOAT   = 'float';
    const INTEGER = 'integer';
    const STRING  = 'string';

    // complex

}

