<?php

declare(strict_types=1);

/**
 * Validator.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-07
 */

namespace Ares;

use Ares\Error\ErrorMessageRenderer;
use Ares\Error\ErrorMessageRendererInterface;
use Ares\Exception\InvalidValidationOptionException;
use Ares\RuleFactory;
use Ares\Rule\BlankableRule;
use Ares\Rule\NullableRule;
use Ares\Rule\RequiredRule;
use Ares\Rule\TypeRule;
use Ares\Rule\UnknownRule;
use Ares\Schema\Parser;
use Ares\Schema\Schema;
use Ares\Schema\Type;

/**
 * Class Validator
 */
class Validator
{
    /** @const array OPTIONS_DEFAULTS */
    private const OPTIONS_DEFAULTS = [
        Option::ALLOW_UNKNOWN => false,
        Option::ALL_BLANKABLE => false,
        Option::ALL_NULLABLE  => false,
        Option::ALL_REQUIRED  => false,
    ];

    /** @var \Ares\Context $context */
    protected $context;
    /** @var \Ares\Error\ErrorMessageRendererInterface $errorMessageRenderer */
    protected $errorMessageRenderer;
    /** @var array $options */
    protected $options;
    /** @var \Ares\RuleFactory */
    protected $ruleFactory;
    /** @var \Ares\Schema\Schema $schema */
    protected $schema;

    /**
     * @param array                  $schema      Validation schema.
     * @param array                  $options     Validation options.
     * @param \Ares\RuleFactory|null $ruleFactory Validation rule factory.
     * @throws \Ares\Exception\InvalidValidationOptionException
     * @throws \Ares\Exception\InvalidValidationSchemaException
     */
    public function __construct(array $schema, array $options = [], ?RuleFactory $ruleFactory = null)
    {
        $this->ruleFactory = $ruleFactory ?? new RuleFactory();
        $this->options = $this->prepareOptions($options);
        $this->schema = $this->prepareSchema($schema, $this->ruleFactory);
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return isset($this->context) ? $this->context->getErrors() : [];
    }

    /**
     * @return \Ares\Error\ErrorMessageRendererInterface
     */
    public function getErrorMessageRenderer(): ErrorMessageRendererInterface
    {
        if (!isset($this->errorMessageRenderer)) {
            $this->errorMessageRenderer = new ErrorMessageRenderer();
        }

        return $this->errorMessageRenderer;
    }

    /**
     * @return \Ares\RuleFactory
     */
    public function getRuleFactory(): RuleFactory
    {
        return $this->ruleFactory;
    }

    /**
     * @param \Ares\Schema\Schema $schema Validation schema.
     * @param mixed               $data   Input data.
     * @param mixed               $field  Current field name or index (part of source reference).
     * @return void
     * @throws \Ares\Exception\InvalidValidationRuleArgsException
     * @throws \Ares\Exception\UnknownValidationRuleIdException
     */
    protected function performValidation(Schema $schema, $data, $field): void
    {
        $this->context->enter($field, $schema);

        if ($this->runBuiltinValidationRules($schema, $data)) {
            switch ($schema->getRule(TypeRule::ID)->getArgs()) {
                case Type::LIST:
                    $this->performListValidation($schema->getSchema(), $data);

                    break;
                case Type::MAP:
                    // no break
                case Type::TUPLE:
                    $this->performMapValidation($schema->getSchemas(), $data);

                    break;
                default:
                    $this->performFieldValidation($schema->getRules(), $data);

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
     * @param \Ares\Schema\Schema $schema Validation schema.
     * @param mixed               $data   Input data.
     * @return void
     */
    protected function performListValidation(Schema $schema, $data): void
    {
        foreach ($data as $key => $value) {
            $this->performValidation($schema, $value, $key);
        }
    }

    /**
     * @param array $schemas Validation schemas.
     * @param mixed $data    Input data.
     * @return void
     */
    protected function performMapValidation(array $schemas, $data): void
    {
        foreach ($schemas as $field => $schema) {
            $this->performValidation($schema, $data[$field] ?? null, $field);
        }
    }

    /**
     * @param array $options User provided options.
     * @return array
     * @throws \Ares\Exception\InvalidValidationOptionException
     */
    protected function prepareOptions(array $options): array
    {
        $expectedOptions = [
            Option::ALLOW_UNKNOWN => 'boolean',
            Option::ALL_BLANKABLE => 'boolean',
            Option::ALL_NULLABLE => 'boolean',
            Option::ALL_REQUIRED => 'boolean',
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
     * Sets the schema.
     *
     * @param array             $schema      Validation schema.
     * @param \Ares\RuleFactory $ruleFactory Validation rule factory.
     * @return \Ares\Schema\Schema
     * @throws \Ares\Exception\InvalidValidationSchemaException
     */
    protected function prepareSchema(array $schema, RuleFactory $ruleFactory): Schema
    {
        return (new Parser($ruleFactory))->parse($schema);
    }

    /**
     * @param \Ares\Schema\Schema $schema Validation schema.
     * @param mixed               $data   Input data.
     * @return bool
     */
    protected function runBuiltinValidationRules(Schema $schema, $data): bool
    {
        $rules = [
            RequiredRule::ID  => $this->options[Option::ALL_REQUIRED],
            TypeRule::ID      => null,
            NullableRule::ID  => $this->options[Option::ALL_NULLABLE],
            UnknownRule::ID   => $this->options[Option::ALLOW_UNKNOWN],
            BlankableRule::ID => $this->options[Option::ALL_BLANKABLE],
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
     * @param \Ares\Error\ErrorMessageRendererInterface $errorMessageRenderer Error message renderer.
     * @return self
     */
    public function setErrorMessageRenderer(ErrorMessageRendererInterface $errorMessageRenderer): self
    {
        $this->errorMessageRenderer = $errorMessageRenderer;

        return $this;
    }

    /**
     * @param mixed $data Input data.
     * @return boolean
     * @throws \Ares\Exception\InvalidValidationRuleArgsException
     * @throws \Ares\Exception\UnknownValidationRuleIdException
     */
    public function validate($data): bool
    {
        $this->context = new Context($data, $this->getErrorMessageRenderer());

        $this->performValidation($this->schema, $data, '');

        return !$this->context->hasErrors();
    }
}

