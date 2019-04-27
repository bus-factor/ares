<?php

declare(strict_types=1);

/**
 * Ares.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-22
 */

namespace Ares;

use Ares\Exception\InvalidSchemaException;
use Ares\Exception\InvalidValidationOptionException;
use Ares\Sanitization\Sanitizer;
use Ares\Schema\Parser;
use Ares\Schema\Schema;
use Ares\Validation\RuleFactory;
use Ares\Validation\Validator;

/**
 * Class Ares
 */
class Ares
{
    /** @var RuleFactory $ruleFactory */
    protected $ruleFactory;
    /** @var Sanitizer $sanitizer */
    protected $sanitizer;
    /** @var Schema $schema */
    protected $schema;
    /** @var Validator $validator */
    protected $validator;

    /**
     * @param array       $schema      Schema definition.
     * @param RuleFactory $ruleFactory Validation rule factory instance.
     * @throws InvalidSchemaException
     */
    public function __construct(array $schema, ?RuleFactory $ruleFactory = null)
    {
        $this->ruleFactory = $ruleFactory ?? new RuleFactory();

        $this->schema = (new Parser($this->ruleFactory))->parse($schema);

        $this->sanitizer = new Sanitizer($this->schema);
        $this->validator = new Validator($this->schema, $this->ruleFactory);
    }

    /**
     * @return Sanitizer
     */
    public function getSanitizer(): Sanitizer
    {
        return $this->sanitizer;
    }

    /**
     * @return array
     */
    public function getValidationErrors(): array
    {
        return $this->getValidator()->getErrors();
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
     * @throws InvalidValidationOptionException
     */
    public function validate($data, array $options = []): bool
    {
        return $this->getValidator()->validate($data, $options);
    }
}

