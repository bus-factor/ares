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
use Ares\Validation\Schema\PhpType;
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
    ];

    /** @const array TYPE_MAPPING */
    const TYPE_MAPPING = [
        PhpType::ARRAY           => Type::MAP,
        PhpType::BOOLEAN         => Type::BOOLEAN,
        PhpType::DOUBLE          => Type::FLOAT,
        PhpType::INTEGER         => Type::INTEGER,
        PhpType::NULL            => null,
        PhpType::OBJECT          => null,
        PhpType::RESOURCE        => null,
        PhpType::RESOURCE_CLOSED => null,
        PhpType::STRING          => Type::STRING,
        PhpType::UNKNOWN         => null,
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
        if (!isset(self::RULE_CLASSMAP[$ruleId])) {
            throw new InvalidArgumentException("Unknown rule ID: {$ruleId}");
        }

        $className = self::RULE_CLASSMAP[$ruleId];

        return new $className();
    }

    /**
     * @param mixed $data Input data.
     * @return boolean
     */
    public function validate($data): bool
    {
        $this->prepareValidation();
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
        $this->context->pushSourceReference($field);

        $phpType = gettype($data);

        if (self::TYPE_MAPPING[$phpType] == $schema['type']) {
            if ($schema['type'] == Type::MAP) {
                $this->performMapValidation($schema['schema'], $data);
            } else {
                foreach ($schema as $ruleId => $ruleConfig) {
                    if (!in_array($ruleId, ['required', 'type', 'schema', 'nullable'])) {
                        call_user_func_array($this->getRule($ruleId), [$ruleConfig, $data, $this->context]);
                    }
                }
            }
        } elseif ($phpType === PhpType::NULL) {
            if (empty($schema['nullable'])) {
                $this->context->addError('nullable', 'Value must not be null');
            }
        } else {
            $this->context->addError('type', 'Invalid type');
        }

        $this->context->popSourceReference();
    }

    /**
     * @param array $schemasByField Schema by field.
     * @param array $data           Input data.
     * @return void
     */
    protected function performMapValidation(array $schemasByField, array $data): void
    {
        foreach ($schemasByField as $field => $schema) {
            if (array_key_exists($field, $data)) {
                $this->performValidation($schema, $data[$field], $field);
            } elseif ($schema['required']) {
                $this->context->pushSourceReference($field);
                $this->context->addError('required', 'Value required');
                $this->context->popSourceReference();
            }
        }

        if ($this->options[Option::ALLOW_UNKNOWN]) {
            return;
        }

        $unknownFields = array_diff_key($data, $schemasByField);

        foreach ($unknownFields as $field => $value) {
            $this->context->pushSourceReference($field);
            $this->context->addError('unknown', 'Unknown field');
            $this->context->popSourceReference();
        }
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
            'required'  => $options[Option::ALL_REQUIRED],
            'blankable' => $options[Option::ALL_BLANKABLE],
            'nullable'  => $options[Option::ALL_NULLABLE],
        ];

        return SchemaSanitizer::sanitize($schema, $schemaDefaults);
    }

    /**
     * @return void
     */
    protected function prepareValidation(): void
    {
        $this->context = new Context();
    }
}

