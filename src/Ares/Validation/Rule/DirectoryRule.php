<?php

declare(strict_types=1);

/**
 * DirectoryRule.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-17
 */

namespace Ares\Validation\Rule;

use Ares\Exception\InapplicableValidationRuleException;
use Ares\Exception\InvalidValidationRuleArgsException;
use Ares\Schema\Type;
use Ares\Validation\Context;

/**
 * Class DirectoryRule
 */
class DirectoryRule extends AbstractRule
{
    public const ID            = 'directory';
    public const ERROR_MESSAGE = 'Directory not found';

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
        if (!is_bool($args)) {
            throw new InvalidValidationRuleArgsException(
                'Invalid args: ' . json_encode($args)
            );
        }

        if (!is_string($data)) {
            throw new InapplicableValidationRuleException(
                'This rule applies to <string> types only'
            );
        }

        if (!$args || file_exists($data) && is_dir($data)) {
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
