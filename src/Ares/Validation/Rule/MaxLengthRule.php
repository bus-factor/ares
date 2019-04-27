<?php

declare(strict_types=1);

/**
 * MaxLengthRule.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-23
 */

namespace Ares\Validation\Rule;

use Ares\Exception\InvalidValidationRuleArgsException;
use Ares\Schema\Type;
use Ares\Validation\Context;

/**
 * Class MaxLengthRule
 */
class MaxLengthRule extends AbstractRule
{
    public const ID            = 'maxlength';
    public const ERROR_MESSAGE = 'Value too long';

    /**
     * @return array
     */
    public function getSupportedTypes(): array
    {
        return [
            Type::LIST,
            Type::STRING,
        ];
    }

    /**
     * @param mixed   $args    Validation rule configuration.
     * @param mixed   $data    Input data.
     * @param Context $context Validation context.
     * @return boolean
     * @throws InvalidValidationRuleArgsException
     */
    public function performValidation($args, $data, Context $context): bool
    {
        if (!is_int($args) || $args < 0) {
            throw new InvalidValidationRuleArgsException('Invalid args: ' . json_encode($args));
        }

        if (is_string($data) && strlen($data) <= $args || is_array($data) && count($data) <= $args) {
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

