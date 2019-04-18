<?php

declare(strict_types=1);

/**
 * MinRule.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-28
 */

namespace Ares\Rule;

use Ares\Context;
use Ares\Exception\InvalidValidationRuleArgsException;
use Ares\Rule\TypeRule;
use Ares\Schema\Type;

/**
 * Class MinRule
 */
class MinRule extends AbstractRule
{
    const ID            = 'min';
    const ERROR_MESSAGE = 'Value must not be smaller than {value}';

    /**
     * @return array
     */
    public function getSupportedTypes(): array
    {
        return [
            Type::FLOAT,
            Type::INTEGER,
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
        if (!is_numeric($args)) {
            throw new InvalidValidationRuleArgsException('Invalid args: ' . json_encode($args));
        }

        if ($data >= $args) {
            return true;
        }

        $message = $context->getSchema()->getRule(self::ID)->getMessage() ?? self::ERROR_MESSAGE;

        $context->addError(
            self::ID,
            $context->getErrorMessageRenderer()
                ->render($context, self::ID, $message, ['value' => $args])
        );

        return false;
    }
}

