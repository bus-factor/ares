<?php

declare(strict_types=1);

/**
 * Ares.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-22
 */

namespace Ares;

use Ares\Exception\InvalidOptionException;
use Ares\Exception\InvalidSchemaException;
use Ares\Sanitization\Sanitizer;
use Ares\Schema\Parser;
use Ares\Schema\Schema;
use Ares\Validation\Error\ErrorCollection;
use Ares\Validation\Validator;

/**
 * Class Ares
 */
class Ares
{
    /**
     * @var Sanitizer
     */
    private $sanitizer;

    /**
     * @var Schema
     */
    private $schema;

    /**
     * @var Validator
     */
    private $validator;

    /**
     * @param array $schema Schema definition.
     * @throws InvalidSchemaException
     */
    public function __construct(array $schema)
    {
        $this->schema = (new Parser())->parse($schema);
        $this->sanitizer = new Sanitizer($this->schema);
        $this->validator = new Validator($this->schema);
    }

    /**
     * @return Sanitizer
     */
    public function getSanitizer(): Sanitizer
    {
        return $this->sanitizer;
    }

    /**
     * @return ErrorCollection
     */
    public function getValidationErrors(): ErrorCollection
    {
        return new ErrorCollection($this->getValidator()->getErrors());
    }

    /**
     * @return Validator
     */
    public function getValidator(): Validator
    {
        return $this->validator;
    }

    /**
     * Sanitizes the input data.
     *
     * @param mixed $data    Input data.
     * @param array $options Sanitization options.
     * @return mixed
     */
    public function sanitize($data, array $options = [])
    {
        return $this->getSanitizer()->sanitize($data, $options);
    }

    /**
     * @param mixed $data    Input data.
     * @param array $options Validation options.
     * @return bool
     * @throws InvalidOptionException
     */
    public function validate($data, array $options = []): bool
    {
        return $this->getValidator()->validate($data, $options);
    }
}
