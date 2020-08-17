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
    /**
     * @var array
     */
    private $inputs = [];

    /**
     * @var array
     */
    private $inputPosition = [];

    /**
     * @param mixed $input            Input.
     * @param mixed $relInputPosition Current input position.
     */
    public function __construct($input, $relInputPosition)
    {
        $this->inputs[] = $input;
        $this->inputPosition[] = $relInputPosition;
    }

    /**
     * @param mixed $relInputPosition Relative input position.
     * @return self
     */
    public function enter($relInputPosition): self
    {
        $this->inputPosition[] = $relInputPosition;
        $this->inputs[] = $this->getInput()[$relInputPosition];

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
