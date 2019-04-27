<?php

declare(strict_types=1);

/**
 * BlankableRule.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-16
 */

namespace Ares\Validation\Rule;

use Ares\Exception\InvalidValidationRuleArgsException;
use Ares\Schema\Type;
use Ares\Validation\Context;

/**
 * Class BlankableRule
 */
class BlankableRule extends AbstractRule
{
    public const ID            = 'blankable';
    public const ERROR_MESSAGE = 'Value must not be blank';

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
            throw new InvalidValidationRuleArgsException('Invalid args: ' . json_encode($args));
        }

        if ($args || !is_string($data) || trim($data) != '') {
            return true;
        }

        $message = $context->getSchema()->hasRule(self::ID)
            ? ($context->getSchema()->getRule(self::ID)->getMessage() ?? self::ERROR_MESSAGE)
            : self::ERROR_MESSAGE;

        $context->addError(
            self::ID,
            $context->getErrorMessageRenderer()->render($context, self::ID, $message)
        );

        return false;
    }
}

