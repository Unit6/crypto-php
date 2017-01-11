<?php
/*
 * This file is part of the Crypto package.
 *
 * (c) Unit6 <team@unit6websites.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Unit6\Crypto;

use Unit6\Crypto\Generator;
use Unit6\Crypto\Encoder;

/**
 * Random Class
 *
 * Denerating randomized data from various sources.
 */
class Random
{
    /**
     * Encoder
     *
     * @var EncoderInterface
     */
    protected $encoder;

    /**
     * Generator
     *
     * @var GeneratorInterface
     */
    protected $generator;

    /**
     * Create new random instance
     *
     * @param Generator\GeneratorInterface $generator
     * @param Encoder\EncoderInterface   $encoder
     *
     * @return void
     */
    public function __construct(Generator\GeneratorInterface $generator = null, Encoder\EncoderInterface $encoder = null)
    {
        if ( ! $encoder) {
            $encoder = new Encoder\DefaultEncoder;
        }

        if ( ! $generator) {
            $factory = new Generator\GeneratorFactory;
            $generator = $factory->getGenerator();
        }

        $this->setEncoder($encoder);
        $this->setGenerator($generator);
    }

    /**
     * Generate Random Bytes
     *
     * Convenience method to return a random bytes.
     *
     * @return integer
     */
    public static function bytes($length, array $options = [])
    {
        $generator = (isset($options['generator']) ? $options['generator'] : null);
        $encoder   = (isset($options['encoder'])   ? $options['encoder']   : null);

        $random = new self($generator, $encoder);

        return $random->getRandomBytes($length);
    }

    /**
     * Generate Random Integer
     *
     * Convenience method to return a random integer.
     *
     * @return integer
     */
    public static function int($min = 0, $max = PHP_INT_MAX, array $options = [])
    {
        $generator = (isset($options['generator']) ? $options['generator'] : null);
        $encoder   = (isset($options['encoder'])   ? $options['encoder']   : null);

        $random = new self($generator, $encoder);

        return $random->getRandomInteger($min, $max);
    }

    /**
     * Generate Random String
     *
     * Convenience method to return a random string.
     *
     * @return string
     */
    public static function str($length, array $options = [])
    {
        $charset   = (isset($options['charset'])   ? $options['charset']   : null);
        $generator = (isset($options['generator']) ? $options['generator'] : null);
        $encoder   = (isset($options['encoder'])   ? $options['encoder']   : null);

        $random = new self($generator, $encoder);

        return $random->getRandomString($length, $charset);
    }

    /**
     * Get Encoder
     *
     * The currently registered encoder instance.
     *
     * @return Encoder\EncoderInterface
     */
    public function getEncoder()
    {
        return $this->encoder;
    }

    /**
     * Set Encoder
     *
     * @param Encoder\EncoderInterface $encoder
     *
     * @return Random
     */
    public function setEncoder(Encoder\EncoderInterface $encoder)
    {
        $this->encoder = $encoder;

        return $this;
    }

    /**
     * Get Generator
     *
     * The currently registered generator instance.
     *
     * @return Generator\GeneratorInstance
     */
    public function getGenerator()
    {
        return $this->generator;
    }

    /**
     * Set Generator
     *
     * @param Generator\GeneratorInterface $generator
     *
     * @return Random
     */
    public function setGenerator(Generator\GeneratorInterface $generator)
    {
        $this->generator = $generator;

        return $this;
    }

    /**
     * Get Random Bytes
     *
     * Generate a random raw byte string of the specified length.
     *
     * @param integer $length The length of the requested string.
     *
     * @return string A random raw byte string of the specified length.
     */
    public function getRandomBytes($length)
    {
        return $this->encoder->encode($this->generator->generate($length));
    }

    /**
     * Get Random Integer
     *
     * Generate a random integer within the specified range.
     *
     * @param integer $min The minimum expected value. Defaults to 0.
     * @param integer $max The maximum expected value. Defaults to PHP_INT_MAX.
     *
     * @return integer A random integer between the specified values, inclusive.
     */
    public function getRandomInteger($min = 0, $max = PHP_INT_MAX)
    {
        $min = (int) $min;
        $max = (int) $max;
        $range = $max - $min;

        $bits  = $this->getBitsInInteger($range);
        $bytes = $this->getBytesInBits($bits);
        $mask  = (int) ((1 << $bits) - 1);

        do {
            $byteString = $this->generator->generate($bytes);
            $result = hexdec(bin2hex($byteString)) & $mask;
        } while ($result > $range);

        return (int) $result + $min;
    }

    /**
     * Get Random String
     *
     * Generate a random string of the specified length.
     *
     * @param integer $length The length of the requested string.
     *
     * @return string A random string of the specified length, consisting of
     *     characters from the base64 character set.
     */
    public function getRandomString($length, $charset = null)
    {
        $length = (int) $length;

        if ( ! $charset) {
            $charset = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        }

        $charsetLength = strlen($charset);
        $neededBytes = $this->getBytesInBits($length * ($this->getBitsInInteger($charsetLength) + 1));

        $string = '';
        do {
            $byteString = $this->generator->generate($neededBytes);
            for ($i = 0; $i < $neededBytes; ++$i) {
                if (ord($byteString[$i]) > (255 - (255 % $charsetLength))) {
                    continue;
                }
                $string .= $charset[ord($byteString[$i]) % $charsetLength];
            }
        } while (strlen($string) < $length);

        return substr($string, 0, $length);
    }

    /**
     * Get Bits in Integer
     *
     * Determine the number of bits required to represent a given number.
     *
     * For example, the number 64 can be represented in 7 bits (0b1000000),
     * so this method would return 7.
     *
     * @param  integer $number
     *
     * @return integer
     */
    protected function getBitsInInteger($number)
    {
        if ($number == 0) {
            return 0;
        }

        $bits = 1;
        while ($number >>= 1) {
            ++$bits;
        }

        return $bits;
    }

    /**
     * Get Bytes in Bits
     *
     * Determine the number of bytes in the specified number of bits.
     *
     * @param integer $bits
     *
     * @return integer
     */
    protected function getBytesInBits($bits)
    {
        return (int) ceil($bits / 8);
    }
}