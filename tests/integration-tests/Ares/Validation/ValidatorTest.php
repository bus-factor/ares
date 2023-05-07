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
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use RegexIterator;

/**
 * Class ValidatorTest
 */
class ValidatorTest extends TestCase
{
    /**
     * @covers \Ares\Ares
     * @covers \Ares\Validation\Validator
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
    public static function getValidateSamples(): array
    {
        $paths = [];

        $directory = new RecursiveDirectoryIterator(INTEGRATION_TEST_SAMPLES_DIR . '/validation');
        $iterator = new RecursiveIteratorIterator($directory);
        $regex = new RegexIterator($iterator, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);

        foreach ($regex as $files) {
            $key = str_replace(INTEGRATION_TEST_SAMPLES_DIR . '/', '', $files[0]);
            $paths[$key] = [$files[0]];
        }

        return $paths;
    }
}
