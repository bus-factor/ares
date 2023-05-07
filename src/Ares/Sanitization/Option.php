<?php

declare(strict_types=1);

/**
 * Option.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-27
 */

namespace Ares\Sanitization;

/**
 * Class Option
 */
enum Option: string
{
    case PURGE_UNKNOWN = 'purgeUnknown';
    case TRIM_STRINGS = 'trimStrings';
}
