<?php
/*
 * This file is part of the Crypto package.
 *
 * (c) Unit6 <team@unit6websites.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Unit6\Crypto\Generator;

/**
 * Generator Interface
 *
 *
 */
interface GeneratorInterface
{
    /**
     * Low priority source.
     *
     * @var integer
     */
    const PRIORITY_LOW = 1;

    /**
     * Medium priority source.
     *
     * @var integer
     */
    const PRIORITY_MEDIUM = 2;

    /**
     * High priority source.
     *
     * @var integer
     */
    const PRIORITY_HIGH = 3;

    /**
     * Generate a raw string of random bytes.
     *
     * @param integer $size
     *
     * @return string
     */
    public function generate($size);

    /**
     * Generator Supported
     *
     * Check if the generated is supported on the current platform.
     *
     * @return boolean Returns true if the generator is supported, false
     *     otherwise.
     */
    public static function isSupported();

    /**
     * Get Generator Priority.
     *
     * Used by the factory in combination with isSupported() to choose the best
     * possible generator for the current platform.
     *
     * @return integer
     */
    public static function getPriority();
}