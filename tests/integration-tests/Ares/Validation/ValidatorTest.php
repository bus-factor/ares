<?php

declare(strict_types=1);

/**
 * ValidatorTest.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-07
 */

namespace IntegrationTest\Ares\Validation;

use Ares\Validation\Validator;
use PHPUnit\Framework\TestCase;

/**
 * Class ValidatorTest
 *
 * @coversDefaultClass \Ares\Validation\Validator
 */
class ValidatorTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getErrors
     * @covers ::validate
     *
     * @dataProvider getValidateSamples
     *
     * @param string $path Path to validate sample.
     * @return void
     */
    public function testValidate(string $path): void
    {
        include $path;
    }

    /**
     * @return array
     */
    public function getValidateSamples(): array
    {
        $paths = [];
        $files = scandir(INTEGRATION_TEST_SAMPLES_DIR);

        foreach ($files as $file) {
            if (in_array($file, ['.', '..'])) {
                continue;
            }

            $paths[$file] = [
                INTEGRATION_TEST_SAMPLES_DIR . '/' . $file
            ];
        }

        return $paths;
    }
}

