<?php

declare(strict_types=1);

/**
 * Ares.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-22
 */

namespace Ares;

use Ares\RuleFactory;
use Ares\Schema\Parser;
use Ares\Schema\Schema;

/**
 * Class Ares
 */
class Ares
{
    /** @var \Ares\RuleFactory $ruleFactory */
    protected $ruleFactory;
    /** @var \Ares\Schema\Schema $schema */
    protected $schema;

    /**
     * @param array             $schema      Schema definition.
     * @param \Ares\RuleFactory $ruleFactory Validation rule factory instance.
     * @throws \Ares\Exception\InvalidValidationSchemaException
     */
    public function __construct(array $schema, ?RuleFactory $ruleFactory = null)
    {
        $this->ruleFactory = $ruleFactory ?? new RuleFactory();
        $this->schema = (new Parser($this->ruleFactory))->parse($schema);
        $this->sanitizer = new Sanitizer($this->schema);
    }

    /**
     * Sanitizes the input data.
     *
     * @param mixed $data Input data.
     * @return mixed
     */
    public function sanitize($data)
    {
        return $this->sanitizer->sanitize($data);
    }
}

