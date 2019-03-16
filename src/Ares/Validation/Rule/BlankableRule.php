<?php

declare(strict_types=1);

/**
 * BlankableRule.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-16
 */

namespace Ares\Validation\Rule;

/**
 * Class BlankableRule
 */
class BlankableRule
{
    /**
     * @return boolean
     */
    public function __invoke(array $source, $data): bool
    {
        return true;
    }
}

