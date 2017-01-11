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
 * Base64 Encoder
 *
 * Use to standard encoding and decoding.
 */
class Base64Encoder implements EncoderInterface
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
        return str_replace("\n", '', base64_encode($str));
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
        return base64_decode($str);
    }
}
