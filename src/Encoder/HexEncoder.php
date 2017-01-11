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
 * Hex Encoder
 *
 * Use to standard encoding and decoding.
 */
class HexEncoder implements EncoderInterface
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
        return bin2hex($str);
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
        if (function_exists('hex2bin')) {
            return hex2bin($str);
        }

        $str = preg_replace('/[^0-9a-f]/i', '', $str);

        // hex2bin was not introduced until PHP 5.4
        return pack('H*' , $str);
    }
}
