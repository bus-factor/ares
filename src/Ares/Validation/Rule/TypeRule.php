<?php

declare(strict_types=1);

/**
 * TypeRule.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-20
 */

namespace Ares\Validation\Rule;

use Ares\Exception\InvalidValidationRuleArgsException;
use Ares\Schema\Type;
use Ares\Validation\Context;
use BusFactor\Ddd\ValueObject\PhpType;

/**
 * Class TypeRule
 */
class TypeRule extends AbstractRule
{
    public const ID            = 'type';
    public const ERROR_MESSAGE = 'Invalid type';

    /**
     * @const array
     */
    private const TYPE_MAPPING = [
        PhpType::ARRAY   => [Type::LIST, Type::MAP, Type::TUPLE],
        PhpType::BOOLEAN => [Type::BOOLEAN],
        PhpType::DOUBLE  => [Type::FLOAT, Type::NUMERIC],
        PhpType::INTEGER => [Type::INTEGER, Type::NUMERIC],
        PhpType::STRING  => [Type::STRING],
    ];

    /**
     * @return array
     */
    public function getSupportedTypes(): array
    {
        return Type::getValidValues();
    }

    /**
     * @param Context $context Validation context.
     * @return bool
     */
    public function isApplicable(Context $context): bool
    {
        return true;
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
        if (!in_array($args, Type::getValidValues(), true)) {
            throw new InvalidValidationRuleArgsException(
                'Invalid args: ' . json_encode($args)
            );
        }

        $phpType = gettype($data);

        if (
            (
                isset(self::TYPE_MAPPING[$phpType])
                && in_array($args, self::TYPE_MAPPING[$phpType], true)
            )
            || $data === null
        ) {
            return true;
        }

        $message = $this->getErrorMessage(
            $context,
            self::ID,
            self::ERROR_MESSAGE
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
