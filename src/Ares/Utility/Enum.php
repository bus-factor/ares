<?php

declare(strict_types=1);

/**
 * Enum.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-02-21
 */

namespace Ares\Utility;

use ReflectionClass;

/**
 * Class Enum
 */
class Enum
{
    /**
     * @return array
     */
    public static function getValues(): array
    {
        return (new ReflectionClass(static::class))->getConstants();
    }
}
