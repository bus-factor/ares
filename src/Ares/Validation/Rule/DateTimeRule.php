<?php

declare(strict_types=1);

/**
 * DateTimeRule.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-22
 */

namespace Ares\Validation\Rule;

use Ares\Exception\InvalidValidationRuleArgsException;
use Ares\Schema\Type;
use Ares\Validation\Context;
use DateTime;

/**
 * Class DateTimeRule
 */
class DateTimeRule extends AbstractRule
{
    public const ID = 'datetime';
    public const ERROR_MESSAGE = 'Value is not a valid date/time string';

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
        if (is_bool($args)) {
            if (!$args) {
                return true;
            }

            return $this->validateDataProcessability($data, $context);
        }

        if (!is_string($args)) {
            throw new InvalidValidationRuleArgsException(
                'Invalid args: ' . json_encode($args)
            );
        }

        if (!$this->validateDataProcessability($data, $context)) {
            return false;
        }

        if (!$this->validateDateTimeFormat($args, $data, $context)) {
            return false;
        }

        return true;
    }

    /**
     * @param mixed   $data    Input data.
     * @param Context $context Validation context.
     * @return boolean
     */
    private function validateDataProcessability($data, Context $context): bool
    {
        if (!is_string($data)) {
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

        $time = strtotime($data);

        if ($time === false) {
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

    /**
     * @param string  $format  Date/Time format.
     * @param string  $data    Input data.
     * @param Context $context Validation context.
     * @return bool
     * @throws InvalidValidationRuleArgsException
     */
    private function validateDateTimeFormat(
        string $format,
        string $data,
        Context $context
    ): bool {
        $dateTime = DateTime::createFromFormat($format, $data);

        if ($dateTime === false) {
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
