<?php

/**
 * ErrorCollection.php
 *
 * Author: Michael Leßnau <michael.lessnau@gmail.com>
 * Date:   2020-10-18
 */

declare(strict_types=1);

namespace Ares\Validation\Error;

use Ares\Utility\JsonPointer;
use ArrayObject;

/**
 * Class ErrorCollection
 */
class ErrorCollection extends ArrayObject
{
    /**
     * @return array
     */
    public function toArrayJsonApiStyle(): array
    {
        $result = [];
        /** @var array|Error[] $errors */
        $errors = $this->getArrayCopy();

        foreach ($errors as $error) {
            $result[] = [
                'status' => '422',
                'code' => $error->getCode(),
                'title' => 'Unprocessable Entity',
                'detail' => $error->getMessage(),
                'source' => [
                    'pointer' => JsonPointer::encode($error->getSource()),
                ],
                'meta' => $error->getMeta(),
            ];
        }

        return $result;
    }

    /**
     * @return array
     */
    public function toArrayNested(): array
    {
        $result = [];
        /** @var array|Error[] $errors */
        $errors = $this->getArrayCopy();

        foreach ($errors as $error) {
            $keys = array_slice($error->getSource(), 1);
            $iMax = count($keys);
            $resultPtr = &$result;

            foreach ($keys as $i => $key) {
                if (!isset($resultPtr[$key])) {
                    $resultPtr[$key] = [];
                }

                if ($i + 1 == $iMax) {
                    $resultPtr[$key][$error->getCode()] = $error->getMessage();

                    break;
                }

                $resultPtr = &$resultPtr[$key];
            }
        }

        return $result;
    }
}
