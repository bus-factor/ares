<?php

declare(strict_types=1);

/**
 * ForbiddenRule.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-22
 */

namespace Ares\Rule;

use Ares\Context;
use Ares\Exception\InvalidValidationRuleArgsException;

/**
 * Class ForbiddenRule
 */
class ForbiddenRule implements RuleInterface
{
    const ID            = 'forbidden';
    const ERROR_MESSAGE = 'Value forbidden';

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

        if (!in_array($data, $args, true)) {
            return true;
        }

        $context->addError(
            self::ID,
            $context->getErrorMessageRenderer()->render($context, self::ID, self::ERROR_MESSAGE)
        );

        return false;
    }
}

