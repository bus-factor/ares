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
     * @param Context $context        Validation context.
     * @param string  $ruleId         Rule ID.
     * @param string  $defaultMessage Rule default validation message.
     * @return string
     */
    protected function getErrorMessage(
        Context $context,
        string $ruleId,
        string $defaultMessage
    ): string {
        $schema = $context->getSchema();

        if (!$schema->hasRule($ruleId)) {
            return $defaultMessage;
        }

        $customMessage = $schema->getRule($ruleId)->getMessage();

        return $customMessage ?? $defaultMessage;
    }

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
    abstract public function performValidation(
        $args,
        $data,
        Context $context
    ): bool;

    /**
     * @param mixed   $args           Validation rule configuration.
     * @param mixed   $data           Input data.
     * @param Context $context        Validation context.
     * @param string  $ruleId         Rule ID.
     * @param string  $defaultMessage Rule validation error message.
     * @return bool|null
     * @throws InvalidValidationRuleArgsException
     */
    protected function performFixedStringFormatValidation(
        $args,
        $data,
        Context $context,
        string $ruleId,
        string $defaultMessage
    ): ?bool {
        if (!is_bool($args)) {
            throw new InvalidValidationRuleArgsException(
                'Invalid args: ' . json_encode($args)
            );
        }

        if (!$args) {
            return true;
        }

        if (!is_string($data)) {
            $message = $this->getErrorMessage(
                $context,
                $ruleId,
                $defaultMessage
            );

            $context->addError(
                $ruleId,
                $context->getErrorMessageRenderer()->render(
                    $context,
                    $ruleId,
                    $message
                )
            );

            return false;
        }

        return null;
    }

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
            $types = implode('>, <', $this->getSupportedTypes());

            throw new InapplicableValidationRuleException(
                sprintf('Rule not applicable. Allowed types: <%s>', $types)
            );
        }

        return $this->performValidation($args, $data, $context);
    }
}
