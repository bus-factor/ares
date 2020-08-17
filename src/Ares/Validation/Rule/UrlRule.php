<?php

declare(strict_types=1);

/**
 * UrlRule.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-28
 */

namespace Ares\Validation\Rule;

use Ares\Exception\InvalidValidationRuleArgsException;
use Ares\Schema\Type;
use Ares\Validation\Context;

/**
 * Class UrlRule
 */
class UrlRule extends AbstractRule
{
    public const ID            = 'url';
    public const ERROR_MESSAGE = 'Invalid URL';

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
     * @param mixed   $args    Validation rule configuration.
     * @param mixed   $data    Input data.
     * @param Context $context Validation context.
     * @return boolean
     * @throws InvalidValidationRuleArgsException
     */
    public function performValidation($args, $data, Context $context): bool
    {
        $validationResult = $this->performFixedStringFormatValidation(
            $args,
            $data,
            $context,
            self::ID,
            self::ERROR_MESSAGE
        );

        if ($validationResult !== null) {
            return $validationResult;
        }

        if (filter_var($data, FILTER_VALIDATE_URL) === false) {
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

        return true;
    }
}
