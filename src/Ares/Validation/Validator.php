<?php

declare(strict_types=1);

/**
 * Validator.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-07
 */

namespace Ares\Validation;

use Ares\Validation\Schema\Type;

/**
 * Class Validator
 */
class Validator
{
    /** @const array TYPE_MAPPING */
    const TYPE_MAPPING = [
        'boolean'           => Type::BOOLEAN,
        'integer'           => Type::INTEGER,
        'double'            => Type::FLOAT,
        'string'            => Type::STRING,
        'array'             => null,
        'object'            => null,
        'resource'          => null,
        'resource (closed)' => null,
        'NULL'              => null,
        'unknown type'      => null,
    ];

    protected $errors = [];
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
     */
    public function validate($data): bool
    {
        $this->errors = [];

        // ------------

        $source = [''];
        $schema = $this->schema;

        // ------------

        $phpType = gettype($data);
        $type = self::TYPE_MAPPING[$phpType];

        if ($type !== $schema['type']) {
            $this->errors[] = new Error($source, 'type', 'Invalid type');
        }

        // ------------

        return empty($this->errors);
    }
}
