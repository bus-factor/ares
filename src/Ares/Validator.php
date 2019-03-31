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
use Ares\Exception\InvalidValidationSchemaException;
use Ares\Exception\UnknownValidationRuleIdException;
use Ares\Rule\AllowedRule;
use Ares\Rule\BlankableRule;
use Ares\Rule\DateTimeRule;
use Ares\Rule\EmailRule;
use Ares\Rule\ForbiddenRule;
use Ares\Rule\MaxLengthRule;
use Ares\Rule\MaxRule;
use Ares\Rule\MinLengthRule;
use Ares\Rule\MinRule;
use Ares\Rule\NullableRule;
use Ares\Rule\RegexRule;
use Ares\Rule\RequiredRule;
use Ares\Rule\TypeRule;
use Ares\Rule\UnknownRule;
use Ares\Rule\UrlRule;
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

    /** @const array RESERVED_RULE_IDS */
    const RESERVED_RULE_IDS = [
        'schema'          => 'schema',
        BlankableRule::ID => BlankableRule::ID,
        NullableRule::ID  => NullableRule::ID,
        RequiredRule::ID  => RequiredRule::ID,
        TypeRule::ID      => TypeRule::ID,
        UnknownRule::ID   => UnknownRule::ID,
    ];

    /** @const array RULE_CLASSMAP */
    const RULE_CLASSMAP = [
        AllowedRule::ID   => AllowedRule::class,
        BlankableRule::ID => BlankableRule::class,
        DateTimeRule::ID  => DateTimeRule::class,
        EmailRule::ID     => EmailRule::class,
        ForbiddenRule::ID => ForbiddenRule::class,
        MaxLengthRule::ID => MaxLengthRule::class,
        MaxRule::ID       => MaxRule::class,
        MinLengthRule::ID => MinLengthRule::class,
        MinRule::ID       => MinRule::class,
        NullableRule::ID  => NullableRule::class,
        RegexRule::ID     => RegexRule::class,
        RequiredRule::ID  => RequiredRule::class,
        TypeRule::ID      => TypeRule::class,
        UnknownRule::ID   => UnknownRule::class,
        UrlRule::ID       => UrlRule::class,
    ];

    /** @var \Ares\Context $context */
    protected $context;
    /** @var array $options */
    protected $options;
    /** @var array $schema */
    protected $schema;

    /**
     * @param array                                          $schema               Validation schema.
     * @param array                                          $options              Validation options.
     * @param \Ares\Error\ErrorMessageRendererInterface|null $errorMessageRenderer Error message renderer instance.
     * @throws \Ares\Exception\InvalidValidationSchemaException
     */
    public function __construct(
        array $schema,
        array $options = [],
        ?ErrorMessageRendererInterface $errorMessageRenderer = null
    ) {
        $this->options = $options + self::OPTIONS_DEFAULTS;
        $this->schema = $this->prepareSchema($schema, $this->options);

        $this->errorMessageRenderer = ($errorMessageRenderer === null)
            ? new ErrorMessageRenderer()
            : $errorMessageRenderer;
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
     * @param string $ruleId Validation rule ID.
     * @return mixed
     * @throws \Ares\Exception\UnknownValidationRuleIdException
     */
    protected function getRule(string $ruleId)
    {
        if (empty(self::RULE_CLASSMAP[$ruleId])) {
            throw new UnknownValidationRuleIdException("Unknown validation rule ID: {$ruleId}");
        }

        $className = self::RULE_CLASSMAP[$ruleId];

        return new $className($this->errorMessageRenderer);
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

        $valid = $this->getRule(RequiredRule::ID)->validate($schema[RequiredRule::ID], $data, $this->context)
            && $this->getRule(UnknownRule::ID)->validate($this->options[Option::ALLOW_UNKNOWN], $data, $this->context)
            && $this->getRule(TypeRule::ID)->validate($schema[TypeRule::ID], $data, $this->context)
            && $this->getRule(NullableRule::ID)->validate($schema[NullableRule::ID], $data, $this->context)
            && $this->getRule(BlankableRule::ID)->validate($schema[BlankableRule::ID], $data, $this->context);

        if ($valid) {
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
                    if (!isset(self::RESERVED_RULE_IDS[$ruleId]) && !$this->getRule($ruleId)->validate($ruleArgs, $data, $this->context)) {
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

