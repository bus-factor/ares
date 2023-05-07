<?php

declare(strict_types=1);

/**
 * SanitizerTest.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-04-25
 */

namespace IntegrationTest\Ares\Sanitization;

use Ares\Sanitization\Sanitizer;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use RegexIterator;

/**
 * Class SanitizerTest
 */
class SanitizerTest extends TestCase
{
    /**
     * @covers \Ares\Ares
     * @covers \Ares\Sanitization\Sanitizer
     *
     * @dataProvider getSanitizeSamples
     *
     * @param string $path Path to validate sample.
     * @return void
     */
    public function testSanitize(string $path): void
    {
        include $path;
    }

    /**
     * @return array
     */
    public static function getSanitizeSamples(): array
    {
        $paths = [];

        $directory = new RecursiveDirectoryIterator(INTEGRATION_TEST_SAMPLES_DIR . '/sanitization');
        $iterator = new RecursiveIteratorIterator($directory);
        $regex = new RegexIterator($iterator, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);

        foreach ($regex as $files) {
            $key = str_replace(INTEGRATION_TEST_SAMPLES_DIR . '/', '', $files[0]);
            $paths[$key] = [$files[0]];
        }

        return $paths;
    }
}
