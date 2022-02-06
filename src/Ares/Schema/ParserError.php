<?php

declare(strict_types=1);

/**
 * ParserError.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-10
 */

namespace Ares\Schema;

use BusFactor\Ddd\ValueObject\Enum;

/**
 * Class ParserError
 */
class ParserError extends Enum
{
    public const TYPE_MISSING         = 0;
    public const TYPE_REPEATED        = 1;
    public const TYPE_UNKNOWN         = 2;
    public const SCHEMA_MISSING       = 3;
    public const RULE_ID_UNKNOWN      = 4;
    public const VALUE_TYPE_MISMATCH  = 5;
    public const RULE_MISSING         = 6;
    public const RULE_AMBIGUOUS       = 7;
    public const RULE_INAPPLICABLE    = 8;
}
