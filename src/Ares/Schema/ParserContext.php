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
    private array $inputs = [];

    private array $inputPosition = [];

    public function __construct(mixed $input, string|int $relInputPosition)
    {
        $this->inputs[] = $input;
        $this->inputPosition[] = $relInputPosition;
    }

    public function enter(string|int $relInputPosition): self
    {
        $this->inputPosition[] = $relInputPosition;
        $this->inputs[] = $this->getInput()[$relInputPosition];

        return $this;
    }

    public function getInput(): mixed
    {
        return end($this->inputs);
    }

    public function getInputPosition(): array
    {
        return $this->inputPosition;
    }

    public function leave(): self
    {
        array_pop($this->inputs);
        array_pop($this->inputPosition);

        return $this;
    }
}
