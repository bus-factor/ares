<?php

declare(strict_types=1);

/**
 * TypeRule.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-20
 */

namespace Ares\Rule;

use Ares\Context;
use Ares\Exception\InvalidValidationRuleArgsException;
use Ares\Schema\Type;
use Ares\Utility\PhpType;

/**
 * Class TypeRule
 */
class TypeRule implements RuleInterface
{
    const ID            = 'type';
    const ERROR_MESSAGE = 'Invalid type';

    /* @const array TYPE_MAPPING maps PHP types to validator specific types */
    const TYPE_MAPPING = [
        PhpType::ARRAY   => [Type::LIST, Type::MAP],
        PhpType::BOOLEAN => [Type::BOOLEAN],
        PhpType::DOUBLE  => [Type::FLOAT],
        PhpType::INTEGER => [Type::INTEGER],
        PhpType::STRING  => [Type::STRING],
    ];

    /**
     * @param mixed         $args    Validation rule configuration.
     * @param mixed         $data    Input data.
     * @param \Ares\Context $context Validation context.
     * @return boolean
     * @throws \Ares\Exception\InvalidValidationRuleArgsException
     */
    public function validate($args, $data, Context $context): bool
    {
        if (!in_array($args, Type::getValues(), true)) {
            throw new InvalidValidationRuleArgsException('Invalid args: ' . json_encode($args));
        }

        $phpType = gettype($data);

        if (isset(self::TYPE_MAPPING[$phpType]) && in_array($args, self::TYPE_MAPPING[$phpType], true) || $data === null) {
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

