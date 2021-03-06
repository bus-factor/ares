<?php

declare(strict_types=1);

/**
 * MinLengthRule.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-23
 */

namespace Ares\Validation\Rule;

use Ares\Exception\InvalidValidationRuleArgsException;
use Ares\Schema\Type;
use Ares\Validation\Context;
use BusFactor\Ddd\ValueObject\PhpType;

/**
 * Class MinLengthRule
 */
class MinLengthRule extends AbstractRule
{
    public const ID = 'minlength';

    public const ERROR_MESSAGES = [
        PhpType::ARRAY => 'Too few items',
        PhpType::STRING => 'Value too short',
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
            throw new InvalidValidationRuleArgsException(
                'Invalid args: ' . json_encode($args)
            );
        }

        $dataType = gettype($data);

        if (
            $dataType === PhpType::STRING && strlen($data) >= $args
            || $dataType === PhpType::ARRAY && count($data) >= $args
        ) {
            return true;
        }

        $message = $this->getErrorMessage(
            $context,
            self::ID,
            self::ERROR_MESSAGES[$dataType]
        );

        $context->addError(
            self::ID,
            $context->getErrorMessageRenderer()->render(
                $context,
                self::ID,
                $message
            )
        );

        return false;
    }
}
