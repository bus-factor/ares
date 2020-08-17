<?php

declare(strict_types=1);

/**
 * NullableRule.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-20
 */

namespace Ares\Validation\Rule;

use Ares\Exception\InvalidValidationRuleArgsException;
use Ares\Schema\Type;
use Ares\Validation\Context;

/**
 * Class NullableRule
 */
class NullableRule extends AbstractRule
{
    public const ID            = 'nullable';
    public const ERROR_MESSAGE = 'Value must not be null';

    /**
     * @return array
     */
    public function getSupportedTypes(): array
    {
        return Type::getValues();
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
        if (!is_bool($args)) {
            throw new InvalidValidationRuleArgsException(
                'Invalid args: ' . json_encode($args)
            );
        }

        if ($args || $data !== null) {
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
