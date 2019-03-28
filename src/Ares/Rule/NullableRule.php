<?php

declare(strict_types=1);

/**
 * NullableRule.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-20
 */

namespace Ares\Rule;

use Ares\Context;
use Ares\Exception\InvalidValidationRuleArgsException;

/**
 * Class NullableRule
 */
class NullableRule implements RuleInterface
{
    const ID            = 'nullable';
    const ERROR_MESSAGE = 'Value must not be null';

    /**
     * @param mixed         $args    Validation rule configuration.
     * @param mixed         $data    Input data.
     * @param \Ares\Context $context Validation context.
     * @return boolean
     * @throws \Ares\Exception\InvalidValidationRuleArgsException
     */
    public function validate($args, $data, Context $context): bool
    {
        if (!is_bool($args)) {
            throw new InvalidValidationRuleArgsException('Invalid args: ' . json_encode($args));
        }

        if ($args || $data !== null) {
            return true;
        }

        $context->addError(
            self::ID,
            $context->getErrorMessageRenderer()->render($context, self::ID, self::ERROR_MESSAGE)
        );

        return false;
    }
}

