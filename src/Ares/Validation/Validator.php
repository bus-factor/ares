<?php

declare(strict_types=1);

/**
 * Validator.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-07
 */

namespace Ares\Validation;

/**
 * Class Validator
 */
class Validator
{
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
     * @param mixed $document Input document.
     * @return boolean
     */
    public function validate($document): bool
    {
        $this->errors = [];

        return empty($this->errors);
    }
}
