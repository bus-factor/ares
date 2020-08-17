<?php

declare(strict_types=1);

/**
 * AbstractRule.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-17
 */

namespace Ares\Validation\Rule;

use Ares\Exception\InapplicableValidationRuleException;
use Ares\Exception\InvalidValidationRuleArgsException;
use Ares\Validation\Context;

/**
 * Class AbstractRule
 */
abstract class AbstractRule implements RuleInterface
{
    /**
     * @return array
     */
    abstract public function getSupportedTypes(): array;

    /**
     * @param Context $context Validation context.
     * @return bool
     */
    public function isApplicable(Context $context): bool
    {
        $schema = $context->getSchema();
        $typePerSchema = $schema->getRule(TypeRule::ID)->getArgs();
        $supportedTypes = $this->getSupportedTypes();

        return in_array($typePerSchema, $supportedTypes, true);
    }

    /**
     * @param mixed   $args    Validation rule configuration.
     * @param mixed   $data    Input data.
     * @param Context $context Validation context.
     * @return boolean
     * @throws InapplicableValidationRuleException;
     * @throws InvalidValidationRuleArgsException
     */
    abstract public function performValidation($args, $data, Context $context): bool;

    /**
     * @param mixed   $args    Validation rule configuration.
     * @param mixed   $data    Input data.
     * @param Context $context Validation context.
     * @return boolean
     * @throws InapplicableValidationRuleException;
     * @throws InvalidValidationRuleArgsException
     */
    public function validate($args, $data, Context $context): bool
    {
        if (!$this->isApplicable($context)) {
            throw new InapplicableValidationRuleException(
                sprintf('This rule is only applicable to the type(s) <%s>', implode('>, <', $this->getSupportedTypes()))
            );
        }

        return $this->performValidation($args, $data, $context);
    }
}

