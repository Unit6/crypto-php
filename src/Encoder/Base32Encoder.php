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
 * Base32 Encoder
 *
 * Based on RFC 4648 standard for encoding and decoding Base32.
 */
class Base32Encoder implements EncoderInterface
{
    /**
     * Defined Charset
     *
     * @var string
     */
    private $charset = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567=';

    /**
     * Encode a string of raw bytes.
     *
     * @param string $str
     *
     * @return string
     */
    public function encode($str)
    {
        $encoded = '';

        if ($str) {
            $binStr = '';
            // Build a binary string representation of the input string
            foreach (str_split($str) as $char) {
                $binStr .= str_pad(decbin(ord($char)), 8, 0, STR_PAD_LEFT);
            }

            // Encode the data in 5-bit chunks
            foreach (str_split($binStr, 5) as $chunk) {
                $chunk = str_pad($chunk, 5, 0, STR_PAD_RIGHT);
                $encoded .= $this->charset[bindec($chunk)];
            }

            // Add padding to the encoded string as required
            if (strlen($encoded) % 8) {
                $encoded .= str_repeat($this->charset[32], 8 - (strlen($encoded) % 8));
            }
        }

        return $encoded;
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
        $decoded = '';
        $str = preg_replace("/[^{$this->charset}]/", '', rtrim(strtoupper($str), $this->charset[32]));

        if ($str) {
            $binStr = '';
            foreach (str_split($str) as $char) {
                $binStr .= str_pad(decbin(strpos($this->charset, $char)), 5, 0, STR_PAD_LEFT);
            }

            $binStr = substr($binStr, 0, (floor(strlen($binStr) / 8) * 8));
            foreach (str_split($binStr, 8) as $chunk) {
                $chunk = str_pad($chunk, 8, 0, STR_PAD_RIGHT);
                $decoded .= chr(bindec($chunk));
            }
        }

        return $decoded;
    }
}
