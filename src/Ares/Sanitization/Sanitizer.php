<?php

declare(strict_types=1);

/**
 * Sanitizer.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-21
 */

namespace Ares\Sanitization;

use Ares\Schema\Schema;
use Ares\Schema\Type;
use Ares\Utility\PhpType;
use Ares\Validation\Rule\TypeRule;

/**
 * Class Sanitizer
 */
class Sanitizer
{
    /** @var Schema $schema */
    protected $schema;

    /**
     * @param Schema $schema Schema instance.
     */
    public function __construct(Schema $schema)
    {
        $this->schema = $schema;
    }

    /**
     * @param mixed $data Input data.
     * @return mixed
     */
    protected function performBooleanSanitization($data)
    {
        $type = gettype($data);

        return in_array($type, [PhpType::DOUBLE, PhpType::INTEGER, PhpType::STRING], true)
            ? !empty($data)
            : $data;
    }

    /**
     * @param mixed $data Input data.
     * @return mixed
     */
    protected function performFloatSanitization($data)
    {
        return is_numeric($data)
            ? (float)$data
            : $data;
    }

    /**
     * @param mixed $data Input data.
     * @return mixed
     */
    protected function performIntegerSanitization($data)
    {
        return is_numeric($data)
            ? (int)$data
            : $data;
    }

    /**
     * @param Schema $schema Schema.
     * @param mixed  $data   Input data.
     * @return mixed
     */
    protected function performListSanitization(Schema $schema, $data)
    {
        if (is_array($data)) {
            foreach ($data as $index => $value) {
                $data[$index] = $this->performSanitization($schema, $data[$index]);
            }
        }

        return $data;
    }

    /**
     * @param Schema[] $schemas Schemas.
     * @param mixed    $data    Input data.
     * @return mixed
     */
    protected function performMapSanitization(array $schemas, $data)
    {
        if (is_array($data)) {
            foreach ($schemas as $index => $schema) {
                if (!array_key_exists($index, $data)) {
                    continue;
                }

                $data[$index] = $this->performSanitization($schema, $data[$index]);
            }
        }

        return $data;
    }

    /**
     * @param Schema $schema Schema.
     * @param mixed  $data   Input data.
     * @return mixed
     */
    protected function performSanitization(Schema $schema, $data)
    {
        $type = $schema->getRule(TypeRule::ID)->getArgs();

        switch ($type) {
            case Type::LIST:
                $data = $this->performListSanitization($schema->getSchema(), $data);

                break;
            case Type::MAP:
                // no break
            case Type::TUPLE:
                $data = $this->performMapSanitization($schema->getSchemas(), $data);

                break;
            case Type::FLOAT:
                $data = $this->performFloatSanitization($data);

                break;
            case Type::INTEGER:
                $data = $this->performIntegerSanitization($data);

                break;
            case Type::BOOLEAN:
                $data = $this->performBooleanSanitization($data);

                break;
            default:
                break;
        }

        return $data;
    }

    /**
     * Sanitizes the input data using the provided schema.
     *
     * @param mixed $data    Input data.
     * @param array $options Sanitization options.
     * @return mixed
     */
    public function sanitize($data, array $options = [])
    {
        return $this->performSanitization($this->schema, $data);
    }
}

