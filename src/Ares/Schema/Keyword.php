<?php

declare(strict_types=1);

/**
 * Keyword.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-10
 */

namespace Ares\Schema;

use BusFactor\Ddd\ValueObject\Enum;

/**
 * Class Keyword
 */
class Keyword extends Enum
{
    public const MESSAGE = 'message';
    public const META    = 'meta';
    public const SCHEMA  = 'schema';
}
