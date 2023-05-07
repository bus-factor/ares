<?php

declare(strict_types=1);

/**
 * Sanitizer.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-21
 */

namespace Ares\Sanitization;

use Ares\Exception\InvalidOptionException;
use Ares\Schema\Schema;
use Ares\Schema\SchemaList;
use Ares\Schema\SchemaMap;
use Ares\Schema\SchemaTuple;
use Ares\Schema\Type;
use Ares\Validation\Rule\TypeRule;
use BusFactor\Ddd\ValueObject\PhpType;

/**
 * Class Sanitizer
 */
class Sanitizer
{
    private Schema $schema;

    /**
     * @param Schema $schema Schema instance.
     */
    public function __construct(Schema $schema)
    {
        $this->schema = $schema;
    }

    /**
     * @param array $options User provided options.
     * @return array
     * @throws InvalidOptionException
     */
    private function prepareOptions(array $options): array
    {
        foreach ($options as $key => $value) {
            $optionKey = Option::tryFrom($key);

            if ($optionKey === null) {
                $format = 'Unknown sanitization option key: \'%s\'';

                throw new InvalidOptionException(sprintf($format, $key));
            }

            $type = gettype($value);

            if ($type !== PhpType::BOOLEAN) {
                $format = 'Invalid sanitization option value: '
                    . '\'%s\' must be of type <boolean>, got <%s>';

                throw new InvalidOptionException(
                    sprintf($format, $key, $type)
                );
            }
        }

        return $options + [
            Option::TRIM_STRINGS->value => true,
            Option::PURGE_UNKNOWN->value => true,
        ];
    }

    /**
     * @param mixed $data    Input data.
     * @param array $options Sanitization options.
     * @return mixed
     */
    private function performBooleanSanitization(mixed $data, array $options): mixed
    {
        $type = gettype($data);

        if ($type === PhpType::STRING && ctype_digit($data)) {
            return !empty($data);
        }

        return in_array($type, [PhpType::DOUBLE, PhpType::INTEGER], true)
            ? !empty($data)
            : $data;
    }

    /**
     * @param mixed $data    Input data.
     * @param array $options Sanitization options.
     * @return mixed
     */
    private function performFloatSanitization(mixed $data, array $options): mixed
    {
        return is_numeric($data)
            ? (float)$data
            : $data;
    }

    /**
     * @param mixed $data    Input data.
     * @param array $options Sanitization options.
     * @return mixed
     */
    private function performIntegerSanitization(mixed $data, array $options): mixed
    {
        return is_numeric($data)
            ? (int)$data
            : $data;
    }

    /**
     * @param Schema $schema  Schema.
     * @param mixed  $data    Input data.
     * @param array  $options Sanitization options.
     * @return mixed
     */
    private function performListSanitization(Schema $schema, mixed $data, array $options): mixed
    {
        if (is_array($data)) {
            foreach ($data as $index => $value) {
                $data[$index] = $this->performSanitization($schema, $value, $options);
            }
        }

        return $data;
    }

    /**
     * @param Schema[] $schemas Schemas.
     * @param mixed    $data    Input data.
     * @param array    $options Sanitization options.
     * @return mixed
     */
    private function performMapSanitization(array $schemas, mixed $data, array $options): mixed
    {
        if (is_array($data)) {
            foreach ($schemas as $index => $schema) {
                if (!array_key_exists($index, $data)) {
                    continue;
                }

                $data[$index] = $this->performSanitization($schema, $data[$index], $options);
            }

            if ($options[Option::PURGE_UNKNOWN->value]) {
                $unknownIndices = array_diff(array_keys($data), array_keys($schemas));

                foreach ($unknownIndices as $unknownIndex) {
                    unset($data[$unknownIndex]);
                }
            }
        }

        return $data;
    }

    /**
     * @param Schema $schema  Schema.
     * @param mixed  $data    Input data.
     * @param array  $options Sanitization options.
     * @return mixed
     */
    private function performSanitization(Schema $schema, mixed $data, array $options): mixed
    {
        $type = $schema->getRule(TypeRule::ID)->getArgs();

        switch ($type) {
            case Type::LIST:
                /** @var SchemaList $schema */
                $data = $this->performListSanitization($schema->getSchema(), $data, $options);
                break;
            case Type::MAP:
                /** @var SchemaMap $schema */
                $data = $this->performMapSanitization($schema->getSchemas(), $data, $options);
                break;
            case Type::TUPLE:
                /** @var SchemaTuple $schema */
                $data = $this->performMapSanitization($schema->getSchemas(), $data, $options);
                break;
            case Type::FLOAT:
                $data = $this->performFloatSanitization($data, $options);
                break;
            case Type::INTEGER:
                $data = $this->performIntegerSanitization($data, $options);
                break;
            case Type::BOOLEAN:
                $data = $this->performBooleanSanitization($data, $options);
                break;
            case Type::STRING:
                $data = $this->performStringSanitization($data, $options);
                break;
            default:
                break;
        }

        return $data;
    }

    /**
     * @param mixed $data    Input data.
     * @param array $options Sanitization options.
     * @return mixed
     */
    private function performStringSanitization(mixed $data, array $options): mixed
    {
        return (is_string($data) && $options[Option::TRIM_STRINGS->value])
            ? trim($data)
            : $data;
    }

    /**
     * Sanitizes the input data using the provided schema.
     *
     * @param mixed $data    Input data.
     * @param array $options Sanitization options.
     * @return mixed
     */
    public function sanitize(mixed $data, array $options = []): mixed
    {
        $options = $this->prepareOptions($options);

        return $this->performSanitization($this->schema, $data, $options);
    }
}
