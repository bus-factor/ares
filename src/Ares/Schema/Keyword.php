<?php

declare(strict_types=1);

/**
 * Keyword.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-10
 */

namespace Ares\Schema;

/**
 * Class Keyword
 */
enum Keyword: string
{
    case MESSAGE = 'message';
    case META = 'meta';
    case SCHEMA = 'schema';
}
