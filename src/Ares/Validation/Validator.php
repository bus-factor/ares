<?php

declare(strict_types=1);

/**
 * Validator.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-07
 */

namespace Ares\Validation;

use Ares\Exception\InvalidValidationSchemaException;
use Ares\Validation\Rule\BlankableRule;
use Ares\Validation\Rule\NullableRule;
use Ares\Validation\Rule\RequiredRule;
use Ares\Validation\Rule\TypeRule;
use Ares\Validation\Rule\UnknownRule;
use Ares\Validation\Schema\Sanitizer as SchemaSanitizer;
use Ares\Validation\Schema\Type;
use InvalidArgumentException;

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

    const RULE_CLASSMAP = [
        BlankableRule::ID => BlankableRule::class,
        NullableRule::ID  => NullableRule::class,
        RequiredRule::ID  => RequiredRule::class,
        TypeRule::ID      => TypeRule::class,
        UnknownRule::ID   => UnknownRule::class,
    ];

    /** @var \Ares\Validation\Context $context */
    protected $context;
    /** @var array $options */
    protected $options;
    /** @var array $schema */
    protected $schema;

    /**
     * @param array $schema Validation schema.
     * @param array $options Validation options.
     * @throws \Ares\Exception\InvalidValidationSchemaException
     */
    public function __construct(array $schema, array $options = [])
    {
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
     * @param string $ruleId Validation rule ID.
     * @return mixed
     */
    protected function getRule(string $ruleId)
    {
        $className = self::RULE_CLASSMAP[$ruleId];

        return new $className();
    }

    /**
     * @param mixed $data Input data.
     * @return boolean
     */
    public function validate($data): bool
    {
        $this->prepareValidation($data);
        $this->performValidation($this->schema, $data, '');

        return !$this->context->hasErrors();
    }

    /**
     * @param array $schema Validation schema.
     * @param mixed $data   Input data.
     * @param mixed $field  Current field name or index (part of source reference).
     * @return void
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
            } else {
                // run custom validation rules
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
     * @param mixed $datai Input data.
     * @return void
     */
    protected function prepareValidation(&$data): void
    {
        $this->context = new Context($data);
    }
}

