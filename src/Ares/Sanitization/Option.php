<?php

declare(strict_types=1);

/**
 * Option.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-27
 */

namespace Ares\Sanitization;

use BusFactor\Ddd\ValueObject\Enum;

/**
 * Class Option
 */
class Option extends Enum
{
    public const PURGE_UNKNOWN = 'purgeUnknown';
    public const TRIM_STRINGS  = 'trimStrings';
}
