<?php

declare(strict_types=1);

/**
 * ParserContext.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-13
 */

namespace Ares\Schema;

/**
 * Class ParserContext
 */
class ParserContext
{
    /** @var array $inputs */
    protected $inputs = [];
    /** @var array $inputPosition */
    protected $inputPosition = [];

    /**
     * @param mixed $input                 Input.
     * @param mixed $relativeInputPosition Current input position.
     */
    public function __construct($input, $relativeInputPosition)
    {
        $this->inputs[] = $input;
        $this->inputPosition[] = $relativeInputPosition;
    }

    /**
     * @param mixed $relativeInputPosition Relative input position.
     * @return self
     */
    public function enter($relativeInputPosition): self
    {
        $this->inputPosition[] = $relativeInputPosition;
        $this->inputs[] = $this->getInput()[$relativeInputPosition];

        return $this;
    }

    /**
     * @return mixed
     */
    public function getInput()
    {
        return end($this->inputs);
    }

    /**
     * @return mixed
     */
    public function getInputPosition(): array
    {
        return $this->inputPosition;
    }

    /**
     * @return self
     */
    public function leave(): self
    {
        array_pop($this->inputs);
        array_pop($this->inputPosition);

        return $this;
    }
}

