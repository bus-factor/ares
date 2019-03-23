<?php

declare(strict_types=1);

/**
 * Option.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-15
 */

namespace Ares;

use Ares\Utility\Enum;

/**
 * Class Option
 */
class Option extends Enum
{
    const ALLOW_UNKNOWN = 'allowUnknown';
    const ALL_BLANKABLE = 'allBlankable';
    const ALL_NULLABLE  = 'allNullable';
    const ALL_REQUIRED  = 'allRequired';
}

