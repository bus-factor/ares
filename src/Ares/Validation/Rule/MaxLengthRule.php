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
use Ares\Utility\PhpType;
use Ares\Validation\Context;

/**
 * Class MaxLengthRule
 */
class MaxLengthRule extends AbstractRule
{
    public const ID = 'maxlength';

    public const ERROR_MESSAGES = [
        PhpType::ARRAY => 'Too many items',
        PhpType::STRING => 'Value too long',
    ];

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

        $dataType = gettype($data);

        if (
            $dataType === PhpType::STRING && strlen($data) <= $args
            || $dataType === PhpType::ARRAY && count($data) <= $args
        ) {
            return true;
        }

        $customMessage = $context->getSchema()->getRule(self::ID)->getMessage();

        if (isset($customMessage)) {
            $message = $customMessage;
        } else {
            $message = self::ERROR_MESSAGES[$dataType];
        }

        $context->addError(
            self::ID,
            $context->getErrorMessageRenderer()->render($context, self::ID, $message)
        );

        return false;
    }
}

