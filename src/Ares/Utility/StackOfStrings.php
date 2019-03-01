<?php

declare(strict_types=1);

/**
 * StackOfStrings.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-01
 */

namespace Ares\Utility;

use Ares\Exception\StackEmptyException;

/**
 * Class StackOfStrings
 */
class StackOfStrings
{
    /** @var array $elements */
    private $elements = [];

    /**
     * Initializes the stack.
     *
     * @param array $elements Initial stack elements.
     */
    public function __construct(array $elements = [])
    {
        $this->setElements($elements);
    }

    /**
     * Checks if the stack is empty.
     *
     * @return boolean
     */
    public function empty(): bool
    {
        return empty($this->elements);
    }

    /**
     * Returns all stack elements.
     *
     * @return array
     */
    public function getElements(): array
    {
        return $this->elements;
    }

    /**
     * Returns to value of the top element of the stack without removing it.
     *
     * @return string
     * @throws \Ares\Exception\StackEmptyException
     */
    public function peek(): string
    {
        if (empty($this->elements)) {
            throw new StackEmptyException();
        }

        return end($this->elements);
    }

    /**
     * Pops (removes) the top element from the stack.
     *
     * @return self
     * @throws \Ares\Exception\StackEmptyException
     */
    public function pop(): self
    {
        if (empty($this->elements)) {
            throw new StackEmptyException();
        }

        array_pop($this->elements);

        return $this;
    }

    /**
     * Pushes an element onto the stack.
     *
     * @param string $element Element.
     * @return self
     */
    public function push(string $element): self
    {
        array_push($this->elements, $element);

        return $this;
    }

    /**
     * Sets the stack elements.
     *
     * @param array $elements StackOfStrings elements.
     * @return self
     */
    public function setElements(array $elements): self
    {
        $this->elements = [];

        // elements are intentionally pushed element-wise onto the stack.
        // push() will take care that each element is of type 'string'.

        foreach ($elements as $element) {
            $this->push($element);
        }

        return $this;
    }
}

