<?php

declare(strict_types=1);

/**
 * AllowedRule.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-21
 */

namespace Ares\Rule;

use Ares\Context;
use Ares\Exception\InvalidValidationRuleArgsException;

/**
 * Class AllowedRule
 */
class AllowedRule implements RuleInterface
{
    const ID            = 'allowed';
    const ERROR_MESSAGE = 'Value not allowed';

    /**
     * @param mixed         $args    Validation rule configuration.
     * @param mixed         $data    Input data.
     * @param \Ares\Context $context Validation context.
     * @return boolean
     * @throws \Ares\Exception\InvalidValidationRuleArgsException
     */
    public function validate($args, $data, Context $context): bool
    {
        if (!is_array($args)) {
            throw new InvalidValidationRuleArgsException('Invalid args: ' . json_encode($args));
        }

        if (in_array($data, $args, true)) {
            return true;
        }

        $message = $context->getSchema()->getRule(self::ID)->getMessage() ?? self::ERROR_MESSAGE;

        $context->addError(
            self::ID,
            $context->getErrorMessageRenderer()->render($context, self::ID, $message)
        );

        return false;
    }
}

