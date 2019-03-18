<?php

declare(strict_types=1);

/**
 * BlankableRule.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-16
 */

namespace Ares\Validation\Rule;

use Ares\Validation\Context;

/**
 * Class BlankableRule
 */
class BlankableRule implements RuleInterface
{
    const ID = 'blankable';
    const MESSAGE = 'Value must not be blank';

    /**
     * @param mixed                    $config  Validation rule configuration.
     * @param mixed                    $data    Input data.
     * @param \Ares\Validation\Context $context Validation context.
     * @return boolean
     */
    public function __invoke($config, $data, Context $context): bool
    {
        if ($config === true) {
            return true;
        }

        if (!is_string($data)) {
            return true;
        }

        if (trim($data) != '') {
            return true;
        }

        $context->addError(self::ID, self::MESSAGE);

        return false;
    }
}

