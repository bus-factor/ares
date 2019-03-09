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
use Ares\Validation\Schema\PhpType;
use Ares\Validation\Schema\Type;

/**
 * Class Validator
 */
class Validator
{
    /** @const array SCHEMA_DEFAULTS */
    const SCHEMA_DEFAULTS = [
        'required' => false,
        'blankable' => false,
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

    /** @var array $errors */
    protected $errors = [];
    /** @var array $schema */
    protected $schema;

    /**
     * @param array $schema
     */
    public function __construct(array $schema)
    {
        $this->schema = $schema;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @param mixed $data Input data.
     * @return boolean
     * @throws \Ares\Exception\InvalidValidationSchemaException
     */
    public function validate($data): bool
    {
        $this->errors = [];

        $this->performValidation([], $this->schema, [], $data, '');

        return empty($this->errors);
    }

    /**
     * @param array $source       Source references.
     * @param array $schema       Validation schema.
     * @param array $schemaSource Current validation schema source.
     * @param mixed $data         Input data.
     * @param mixed $field        Current field name or index (part of source reference).
     * @return void
     * @throws \Ares\Exception\InvalidValidationSchemaException
     */
    protected function performValidation(array $source, array $schema, array $schemaSource, $data, $field): void
    {
        if (!isset($schema['type'])) {
            $schemaSourceFormatted = '[\'' . implode('\'][\'', array_merge($schemaSource, ['type'])) . '\']';
            throw new InvalidValidationSchemaException('Missing schema option: $schema' . $schemaSourceFormatted);
        }

        $source[] = $field;
        $schema += $schema + self::SCHEMA_DEFAULTS;

        $phpType = gettype($data);
        $type = self::TYPE_MAPPING[$phpType];

        if ($type == $schema['type']) {
            if ($schema['type'] == Type::STRING) {
                if (!$schema['blankable'] && trim($data) == '') {
                    $this->errors[] = new Error($source, 'blank', 'Value must not be blank');
                }
            } else if ($schema['type'] == Type::MAP) {
                if (!isset($schema['schema'])) {
                    $schemaSourceFormatted = '[\'' . implode('\'][\'', array_merge($schemaSource, ['schema'])) . '\']';
                    throw new InvalidValidationSchemaException('Missing schema option: $schema' . $schemaSourceFormatted);
                }

                $schemaSource[] = 'schema';
                $this->performMapValidation($source, $schema['schema'], $schemaSource, $data);
                array_pop($schemaSource);
            }
        } else if ($phpType === PhpType::NULL) {
            if (!empty($schema['required'])) {
                $this->errors[] = new Error($source, 'required', 'Value required');
            }
        } else {
            $this->errors[] = new Error($source, 'type', 'Invalid type');
        }

        array_pop($source);
    }

    /**
     * @param array $source         Source references.
     * @param array $schemasByField Schema by field.
     * @param array $schemaSource   Current validation schema source.
     * @param array $data           Input data.
     * @return void
     * @throws \Ares\Exception\InvalidValidationSchemaException
     */
    protected function performMapValidation(array $source, array $schemasByField, array $schemaSource, array $data): void
    {
        foreach ($schemasByField as $field => $schema) {
            if (array_key_exists($field, $data)) {
                $schemaSource[] = $field;
                $this->performValidation($source, $schema, $schemaSource, $data[$field], $field);
                array_pop($schemaSource);
            } else {
                $schema = $schema + self::SCHEMA_DEFAULTS;

                if (!empty($schema['required'])) {
                    $source[] = $field;

                    $this->errors[] = new Error($source, 'required', 'Value required');

                    array_pop($source);
                }
            }
        }
    }
}
