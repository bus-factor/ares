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
use Ares\RuleFactory;
use Ares\Rule\BlankableRule;
use Ares\Rule\NullableRule;
use Ares\Rule\RequiredRule;
use Ares\Rule\TypeRule;
use Ares\Rule\UnknownRule;
use Ares\Schema\Sanitizer as SchemaSanitizer;
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
    /** @var array $options */
    protected $options;
    /** @var \Ares\RuleFactory */
    protected $ruleFactory;
    /** @var array $schema */
    protected $schema;

    /**
     * @param array                                          $schema               Validation schema.
     * @param array                                          $options              Validation options.
     * @param \Ares\Error\ErrorMessageRendererInterface|null $errorMessageRenderer Error message renderer instance.
     * @param \Ares\RuleFactory|null                         $ruleFactory          Validation rule factory.
     * @throws \Ares\Exception\InvalidValidationSchemaException
     */
    public function __construct(
        array $schema,
        array $options = [],
        ?ErrorMessageRendererInterface $errorMessageRenderer = null,
        ?RuleFactory $ruleFactory = null
    ) {
        $this->errorMessageRenderer = ($errorMessageRenderer === null)
            ? new ErrorMessageRenderer()
            : $errorMessageRenderer;

        $this->ruleFactory = ($ruleFactory === null)
            ? new RuleFactory()
            : $ruleFactory;

        $this->options = $options + self::OPTIONS_DEFAULTS;
        $this->schema = $this->prepareSchema($schema, $this->options);
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
     * @param array $schema Validation schema.
     * @param mixed $data   Input data.
     * @param mixed $field  Current field name or index (part of source reference).
     * @return void
     * @throws \Ares\Exception\InvalidValidationRuleArgsException
     * @throws \Ares\Exception\UnknownValidationRuleIdException
     */
    protected function performValidation(array $schema, $data, $field): void
    {
        $this->context->enter($field, $schema);

        if ($this->runBuiltinValidationRules($schema, $data)) {
            if ($schema[TypeRule::ID] == Type::MAP) {
                foreach ($schema['schema'] as $childField => $childSchema) {
                    $this->performValidation($childSchema, $data[$childField] ?? null, $childField);
                }
            } elseif ($schema[TypeRule::ID] == Type::LIST) {
                foreach ($data as $listItemKey => $listItemValue) {
                    $this->performValidation($schema['schema'], $listItemValue, $listItemKey);
                }
            } else {
                foreach ($schema as $ruleId => $ruleArgs) {
                    if ($this->ruleFactory->isReserved($ruleId) || $ruleId === 'schema') {
                        continue;
                    }

                    if (!$this->ruleFactory->get($ruleId)->validate($ruleArgs, $data, $this->context)) {
                        break;
                    }
                }
            }
        }

        $this->context->leave();
    }

    /**
     * Sets the schema.
     *
     * @param array $schema  Validation schema.
     * @param array $options Validation options.
     * @return array
     * @throws \Ares\Exception\InvalidValidationSchemaException
     */
    protected function prepareSchema(array $schema, array $options): array
    {
        $schemaDefaults = [
            RequiredRule::ID  => $options[Option::ALL_REQUIRED],
            BlankableRule::ID => $options[Option::ALL_BLANKABLE],
            NullableRule::ID  => $options[Option::ALL_NULLABLE],
        ];

        return SchemaSanitizer::sanitize($schema, $schemaDefaults);
    }

    /**
        * @param array $schema Validation schema.
        * @param mixed $data   Input data.
        * @return bool
     */
    protected function runBuiltinValidationRules(array $schema, $data): bool
    {
        return $this->ruleFactory->get(RequiredRule::ID)->validate($schema[RequiredRule::ID], $data, $this->context)
            && $this->ruleFactory->get(UnknownRule::ID)->validate($this->options[Option::ALLOW_UNKNOWN], $data, $this->context)
            && $this->ruleFactory->get(TypeRule::ID)->validate($schema[TypeRule::ID], $data, $this->context)
            && $this->ruleFactory->get(NullableRule::ID)->validate($schema[NullableRule::ID], $data, $this->context)
            && $this->ruleFactory->get(BlankableRule::ID)->validate($schema[BlankableRule::ID], $data, $this->context);
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
        $this->context = new Context($data, $this->errorMessageRenderer);

        $this->performValidation($this->schema, $data, '');

        return !$this->context->hasErrors();
    }
}

