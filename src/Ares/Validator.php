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
use Ares\Schema\Keyword;
use Ares\Schema\Parser;
use Ares\Schema\Schema;
use Ares\Schema\SchemaList;
use Ares\Schema\SchemaMap;
use Ares\Schema\Type;

/**
 * Class Validator
 */
class Validator
{
    /** @const array OPTIONS_DEFAULTS */
    const OPTIONS_DEFAULTS = [
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
    public function __construct(
        array $schema,
        array $options = [],
        ?RuleFactory $ruleFactory = null
    ) {
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
            $typeAsPerSchema = $schema->getRule(TypeRule::ID)->getArgs();

            if ($typeAsPerSchema == Type::MAP) {
                foreach ($schema->getSchemas() as $childField => $childSchema) {
                    $this->performValidation($childSchema, $data[$childField] ?? null, $childField);
                }
            } elseif ($typeAsPerSchema == Type::LIST) {
                foreach ($data as $listItemKey => $listItemValue) {
                    $this->performValidation($schema->getSchema(), $listItemValue, $listItemKey);
                }
            } else {
                foreach ($schema->getRules() as $ruleId => $rule) {
                    if ($this->ruleFactory->isReserved($ruleId)) {
                        continue;
                    }

                    if (!$this->ruleFactory->get($ruleId)->validate($rule->getArgs(), $data, $this->context)) {
                        break;
                    }
                }
            }
        }

        $this->context->leave();
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
        // required rule

        $args = $schema->hasRule(RequiredRule::ID)
            ? $schema->getRule(RequiredRule::ID)->getArgs()
            : $this->options[Option::ALL_REQUIRED];

        $rule = $this->ruleFactory->get(RequiredRule::ID);

        if (!$rule->validate($args, $data, $this->context)) {
            return false;
        }

        // unknown rule

        $args = $this->options[Option::ALLOW_UNKNOWN];
        $rule = $this->ruleFactory->get(UnknownRule::ID);

        if (!$rule->validate($args, $data, $this->context)) {
            return false;
        }

        // type rule

        $args = $schema->getRule(TypeRule::ID)->getArgs();
        $rule = $this->ruleFactory->get(TypeRule::ID);

        if (!$rule->validate($args, $data, $this->context)) {
            return false;
        }

        // nullable rule

        $args = $schema->hasRule(NullableRule::ID)
            ? $schema->getRule(NullableRule::ID)->getArgs()
            : $this->options[Option::ALL_NULLABLE];

        $rule = $this->ruleFactory->get(NullableRule::ID);

        if (!$rule->validate($args, $data, $this->context)) {
            return false;
        }

        // blankable rule

        $args = $schema->hasRule(BlankableRule::ID)
            ? $schema->getRule(BlankableRule::ID)->getArgs()
            : $this->options[Option::ALL_BLANKABLE];

        $rule = $this->ruleFactory->get(BlankableRule::ID);

        if (!$rule->validate($args, $data, $this->context)) {
            return false;
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

