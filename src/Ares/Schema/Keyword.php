<?php

declare(strict_types=1);

/**
 * Keyword.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-10
 */

namespace Ares\Schema;

use Ares\Utility\Enum;

/**
 * Class Keyword
 */
class Keyword extends Enum
{
    const MESSAGE = 'message';
    const META    = 'meta';
    const SCHEMA  = 'schema';
}

