<?php
/*
 * This file is part of the Crypto package.
 *
 * (c) Unit6 <team@unit6websites.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Unit6\Crypto\Encoder;

/**
 * Default Raw Encoder
 *
 * Effectively a pass-through "encoder".
 */
class DefaultEncoder implements EncoderInterface
{
    /**
     * Encode a string of raw bytes.
     *
     * @param string $str
     *
     * @return string
     */
    public function encode($str)
    {
        return $str;
    }

    /**
     * Decode an encoded string.
     *
     * @param string $str
     *
     * @return string
     */
    public function decode($str)
    {
        return $str;
    }
}
