<?php

declare(strict_types=1);

/**
 * Option.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-15
 */

namespace Ares\Validation;

use Ares\Utility\Enum;

/**
 * Class Option
 */
class Option extends Enum
{
    public const ALL_UNKNOWN_ALLOWED = 'allUnknownAllowed';
    public const ALL_BLANKABLE       = 'allBlankable';
    public const ALL_NULLABLE        = 'allNullable';
    public const ALL_REQUIRED        = 'allRequired';
}
