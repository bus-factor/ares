<?php

declare(strict_types=1);

/**
 * Option.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-15
 */

namespace Ares\Validation;

/**
 * Class Option
 */
enum Option: string
{
    case ALL_UNKNOWN_ALLOWED = 'allUnknownAllowed';
    case ALL_BLANKABLE       = 'allBlankable';
    case ALL_NULLABLE        = 'allNullable';
    case ALL_REQUIRED        = 'allRequired';
}
