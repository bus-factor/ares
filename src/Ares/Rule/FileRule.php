<?php

declare(strict_types=1);

/**
 * FileRule.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-17
 */

namespace Ares\Rule;

use Ares\Context;
use Ares\Exception\InapplicableValidationRuleException;
use Ares\Exception\InvalidValidationRuleArgsException;

/**
 * Class FileRule
 */
class FileRule implements RuleInterface
{
    const ID            = 'file';
    const ERROR_MESSAGE = 'File not found';

    /**
     * @param mixed         $args    Validation rule configuration.
     * @param mixed         $data    Input data.
     * @param \Ares\Context $context Validation context.
     * @return boolean
     * @throws \Ares\Exception\InapplicableValidationRuleException
     * @throws \Ares\Exception\InvalidValidationRuleArgsException
     */
    public function validate($args, $data, Context $context): bool
    {
        if (!is_bool($args)) {
            throw new InvalidValidationRuleArgsException('Invalid args: ' . json_encode($args));
        }

        if (!is_string($data)) {
            throw new InapplicableValidationRuleException('This rule applies to <string> types only');
        }

        if (!$args || file_exists($data) && is_file($data)) {
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

