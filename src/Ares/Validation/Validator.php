<?php

declare(strict_types=1);

/**
 * Validator.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-07
 */

namespace Ares\Validation;

use Ares\Exception\InvalidValidationOptionException;
use Ares\Exception\InvalidValidationRuleArgsException;
use Ares\Exception\UnknownValidationRuleIdException;
use Ares\Schema\Parser;
use Ares\Schema\Schema;
use Ares\Schema\Type;
use Ares\Validation\Error\ErrorMessageRenderer;
use Ares\Validation\Error\ErrorMessageRendererInterface;
use Ares\Validation\RuleFactory;
use Ares\Validation\Rule\BlankableRule;
use Ares\Validation\Rule\NullableRule;
use Ares\Validation\Rule\RequiredRule;
use Ares\Validation\Rule\TypeRule;
use Ares\Validation\Rule\UnknownAllowedRule;

/**
 * Class Validator
 */
class Validator
{
    /** @const array OPTIONS_DEFAULTS */
    private const OPTIONS_DEFAULTS = [
        Option::ALL_UNKNOWN_ALLOWED => false,
        Option::ALL_BLANKABLE       => false,
        Option::ALL_NULLABLE        => false,
        Option::ALL_REQUIRED        => true,
    ];

    /** @var Context $context */
    protected $context;
    /** @var ErrorMessageRendererInterface $errorMessageRenderer */
    protected $errorMessageRenderer;
    /** @var RuleFactory */
    protected $ruleFactory;
    /** @var Schema $schema */
    protected $schema;

    /**
     * @param array            $schema      Schema.
     * @param RuleFactory|null $ruleFactory Validation rule factory.
     */
    public function __construct(Schema $schema, ?RuleFactory $ruleFactory = null)
    {
        $this->ruleFactory = $ruleFactory ?? new RuleFactory();
        $this->schema = $schema;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return isset($this->context) ? $this->context->getErrors() : [];
    }

    /**
     * @return ErrorMessageRendererInterface
     */
    public function getErrorMessageRenderer(): ErrorMessageRendererInterface
    {
        if (!isset($this->errorMessageRenderer)) {
            $this->errorMessageRenderer = new ErrorMessageRenderer();
        }

        return $this->errorMessageRenderer;
    }

    /**
     * @return RuleFactory
     */
    public function getRuleFactory(): RuleFactory
    {
        return $this->ruleFactory;
    }

    /**
     * @param Schema $schema  Schema.
     * @param mixed  $data    Input data.
     * @param mixed  $field   Current field name or index (part of source reference).
     * @param array  $options Validation options.
     * @return void
     * @throws InvalidValidationRuleArgsException
     * @throws UnknownValidationRuleIdException
     */
    protected function performValidation(Schema $schema, $data, $field, array $options): void
    {
        $this->context->enter($field, $schema);

        if ($this->runBuiltinValidationRules($schema, $data, $options)) {
            $this->performFieldValidation($schema->getRules(), $data);

            switch ($schema->getRule(TypeRule::ID)->getArgs()) {
                case Type::LIST:
                    $this->performListValidation($schema->getSchema(), $data, $options);

                    break;
                case Type::MAP:
                    // no break
                case Type::TUPLE:
                    $this->performMapValidation($schema->getSchemas(), $data, $options);

                    break;
                default:
                    // no op
                    break;
            }
        }

        $this->context->leave();
    }

    /**
     * @param array $rules Schema rules.
     * @param mixed $data  Input data.
     * @return void
     */
    protected function performFieldValidation(array $rules, $data): void
    {
        foreach ($rules as $ruleId => $rule) {
            if ($this->ruleFactory->isReserved($ruleId)) {
                continue;
            }

            if (!$this->ruleFactory->get($ruleId)->validate($rule->getArgs(), $data, $this->context)) {
                break;
            }
        }
    }

    /**
     * @param Schema $schema  Schema.
     * @param mixed  $data    Input data.
     * @param array  $options Validation options.
     * @return void
     */
    protected function performListValidation(Schema $schema, $data, array $options): void
    {
        foreach ($data as $key => $value) {
            $this->performValidation($schema, $value, $key, $options);
        }
    }

    /**
     * @param array $schemas Schemas.
     * @param mixed $data    Input data.
     * @param array $options Validation options.
     * @return void
     */
    protected function performMapValidation(array $schemas, $data, array $options): void
    {
        foreach ($schemas as $field => $schema) {
            $this->performValidation($schema, $data[$field] ?? null, $field, $options);
        }
    }

    /**
     * @param array $options User provided options.
     * @return array
     * @throws InvalidValidationOptionException
     */
    protected function prepareOptions(array $options): array
    {
        $expectedOptions = [
            Option::ALL_UNKNOWN_ALLOWED => 'boolean',
            Option::ALL_BLANKABLE       => 'boolean',
            Option::ALL_NULLABLE        => 'boolean',
            Option::ALL_REQUIRED        => 'boolean',
        ];

        foreach ($options as $key => $value) {
            if (!isset($expectedOptions[$key])) {
                throw new InvalidValidationOptionException(
                    sprintf(
                        'Unknown validation option: \'%s\' is not a supported validation option',
                        $key
                    )
                );
            }

            $type = gettype($value);

            if ($type !== $expectedOptions[$key]) {
                throw new InvalidValidationOptionException(
                    sprintf(
                        'Invalid validation option: \'%s\' must be of type <%s>, got <%s>',
                        $key,
                        $expectedOptions[$key],
                        $type
                    )
                );
            }
        }

        return $options + self::OPTIONS_DEFAULTS;
    }

    /**
     * @param Schema $schema  Schema.
     * @param mixed  $data    Input data.
     * @param array  $options Validation options.
     * @return bool
     */
    protected function runBuiltinValidationRules(Schema $schema, $data, array $options): bool
    {
        $rules = [
            RequiredRule::ID       => $options[Option::ALL_REQUIRED],
            TypeRule::ID           => null,
            NullableRule::ID       => $options[Option::ALL_NULLABLE],
            UnknownAllowedRule::ID => $options[Option::ALL_UNKNOWN_ALLOWED],
            BlankableRule::ID      => $options[Option::ALL_BLANKABLE],
        ];

        foreach ($rules as $ruleId => $defaultArgs) {
            $rule = $this->ruleFactory->get($ruleId);

            if ($rule->isApplicable($this->context)) {
                $ruleArgs = $schema->hasRule($ruleId)
                    ? $schema->getRule($ruleId)->getArgs()
                    : $defaultArgs;

                if (!$rule->validate($ruleArgs, $data, $this->context)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @param ErrorMessageRendererInterface $errorMessageRenderer Error message renderer.
     * @return self
     */
    public function setErrorMessageRenderer(ErrorMessageRendererInterface $errorMessageRenderer): self
    {
        $this->errorMessageRenderer = $errorMessageRenderer;

        return $this;
    }

    /**
     * @param mixed $data    Input data.
     * @param array $options Validation options.
     * @return boolean
     * @throws InvalidValidationOptionException
     * @throws InvalidValidationRuleArgsException
     * @throws UnknownValidationRuleIdException
     */
    public function validate($data, array $options = []): bool
    {
        $options = $this->prepareOptions($options);

        $this->context = new Context($data, $this->getErrorMessageRenderer());

        $this->performValidation($this->schema, $data, '', $options);

        return !$this->context->hasErrors();
    }
}

