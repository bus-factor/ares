<?php

declare(strict_types=1);

/**
 * ParserError.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-10
 */

namespace Ares\Schema;

use Ares\Utility\Enum;

/**
 * Class ParserError
 */
class ParserError extends Enum
{
    const TYPE_MISSING         = 0;
    const TYPE_REPEATED        = 1;
    const TYPE_UNKNOWN         = 2;
    const SCHEMA_MISSING       = 3;
    const RULE_ID_UNKNOWN      = 4;
    const VALUE_TYPE_MISMATCH  = 5;
    const RULE_MISSING         = 6;
    const RULE_AMBIGUOUS       = 7;
    const RULE_INAPPLICABLE    = 8;
}
