<?php

declare(strict_types=1);

/**
 * RegexRule.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-30
 */

namespace Ares\Rule;

use Ares\Context;
use Ares\Exception\InvalidValidationRuleArgsException;
use Ares\Rule\TypeRule;
use Ares\Schema\Type;

/**
 * Class RegexRule
 */
class RegexRule extends AbstractRule
{
    const ID            = 'regex';
    const ERROR_MESSAGE = 'Value invalid';

    /**
     * @return array
     */
    public function getSupportedTypes(): array
    {
        return [
            Type::STRING,
        ];
    }

    /**
     * @param mixed         $args    Validation rule configuration.
     * @param mixed         $data    Input data.
     * @param \Ares\Context $context Validation context.
     * @return boolean
     * @throws \Ares\Exception\InvalidValidationRuleArgsException
     */
    public function performValidation($args, $data, Context $context): bool
    {
        if (!is_string($args)) {
            throw new InvalidValidationRuleArgsException('Invalid args: ' . json_encode($args));
        }

        if (preg_match($args, $data) === 1) {
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

