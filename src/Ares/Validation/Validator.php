<?php

declare(strict_types=1);

/**
 * Validator.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-07
 */

namespace Ares\Validation;

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
    ];

    /** @const array TYPE_MAPPING */
    const TYPE_MAPPING = [
        PhpType::BOOLEAN         => Type::BOOLEAN,
        PhpType::INTEGER         => Type::INTEGER,
        PhpType::DOUBLE          => Type::FLOAT,
        PhpType::STRING          => Type::STRING,
        PhpType::ARRAY           => null,
        PhpType::OBJECT          => null,
        PhpType::RESOURCE        => null,
        PhpType::RESOURCE_CLOSED => null,
        PhpType::NULL            => null,
        PhpType::UNKNOWN         => null,
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

        $source = [''];
        $schema = $this->schema + self::SCHEMA_DEFAULTS;

        $phpType = gettype($data);
        $type = self::TYPE_MAPPING[$phpType];

        if ($type == $schema['type']) {
            if ($type == Type::STRING && trim($data) == '') {
                $this->errors[] = new Error($source, 'required', 'Value required');
            }
        } else if ($phpType === PhpType::NULL) {
            if (!empty($schema['required'])) {
                $this->errors[] = new Error($source, 'required', 'Value required');
            }
        } else {
            $this->errors[] = new Error($source, 'type', 'Invalid type');
        }

        return empty($this->errors);
    }
}
