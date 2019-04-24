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
     * Sanitizes the input data using the provided schema.
     *
     * @param mixed $data
     * @return mixed
     */
    public function sanitize($data)
    {
        return $data;
    }
}

