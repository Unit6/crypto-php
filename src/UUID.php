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

use InvalidArgumentException;

/**
 * Universally Unique IDentifier (UUID)
 *
 * Class creates RFC 4122 compliant UUIDs. The generated UUIDs can be
 * version 1, 3, 4, or 5. Functionality is provided to
 * validate UUIDs as well as validate name based UUIDs.
 *
 * @see https://www.ietf.org/rfc/rfc4122
 * @see https://en.wikipedia.org/wiki/UUID
 */
class UUID
{
    /**
     * Version 3 (MD5) UUID
     *
     * @var int
     */
    const MD5 = 3;

    /**
     * Version 5 (SHA1) UUID
     *
     * @var int
     */
    const SHA1 = 5;

    /**
     * 00001111  Clears all bits of version byte with AND
     *
     * @var int
     */
    const CLEAR_VER = 15;

    /**
     * 00111111  Clears all relevant bits of variant byte with AND
     *
     * @var int
     */
    const CLEAR_VAR = 63;

    /**
     * 11100000  Variant reserved for future use
     *
     * @var int
     */
    const VAR_RES = 224;

    /**
     * 11000000  Microsoft UUID variant
     *
     * @var int
     */
    const VAR_MS = 192;

    /**
     * 10000000  The RFC 4122 variant (this variant)
     *
     * @var int
     */
    const VAR_RFC = 128;

    /**
     * 00000000  The NCS compatibility variant
     *
     * @var int
     */
    const VAR_NCS = 0;

    /**
     * 00010000
     *
     * @var int
     */
    const VERSION_1 = 16;

    /**
     * 00110000
     *
     * @var int
     */
    const VERSION_3 = 48;

    /**
     * 01000000
     *
     * @var int
     */
    const VERSION_4 = 64;

    /**
     * 01010000
     *
     * @var int
     */
    const VERSION_5 = 80;

    /**
     * Time (in 100ns steps) between the start of the UTC and Unix epochs
     *
     * Offset between UUID formatted times and Unix formatted times.
     * UUID UTC base time is October 15, 1582.
     * Unix base time is January 1, 1970
     *
     * @var int
     */
    const INTERVAL = 0x01b21dd213814000;

    /**
     * Name string is a fully-qualified domain name
     *
     * @see https://www.ietf.org/rfc/rfc4122
     *
     * @var string
     */
    const NS_DNS = '6ba7b810-9dad-11d1-80b4-00c04fd430c8';

    /**
     * Name string is a URL
     *
     * @see https://www.ietf.org/rfc/rfc4122
     *
     * @var string
     */
    const NS_URL = '6ba7b811-9dad-11d1-80b4-00c04fd430c8';

    /**
     * Name string is an ISO OID
     *
     * @see https://www.ietf.org/rfc/rfc4122
     *
     * @var string
     */
    const NS_OID = '6ba7b812-9dad-11d1-80b4-00c04fd430c8';

    /**
     * Name string is an X.500 DN
     *
     * @see https://www.ietf.org/rfc/rfc4122
     *
     * @var string
     */
    const NS_X500 = '6ba7b814-9dad-11d1-80b4-00c04fd430c8';

    /**
     * UUID Bytes
     *
     * @var integer
     */
    protected $bytes;

    /**
     * UUID Value
     *
     * @var string
     */
    protected $value;

    /**
     * UUID URN
     *
     * @var string
     */
    protected $urn;

    /**
     * UUID Version
     *
     * @var string
     */
    protected $version;

    /**
     * Create UUID Instance
     *
     * @param string $uuid Binary string
     *
     * @throws InvalidArgumentException
     */
    protected function __construct($uuid)
    {
        if ( ! empty($uuid) && strlen($uuid) !== 16) {
            throw new InvalidArgumentException('UUID must be a 128-bit integer');
        }

        $this->bytes = $uuid;
        $this->version = ord($uuid[6]) >> 4;
        $this->value = static::format($uuid);
    }

    /**
     * Get Hexadecimal Representation of UUID
     *
     * @param string $uuid UUID binary data.
     *
     * @param string
     */
    public static function format($uuid)
    {
        $part = function ($start, $end) use ($uuid) {
            return bin2hex(substr($uuid, $start, $end));
        };

        return sprintf(
            '%s-%s-%s-%s-%s',
            $part(0, 4), $part(4, 2), $part(6, 2), $part(8, 2), $part(10, 6)
        );
    }

    /**
     * Validate if a UUID has a valid format.
     *
     * @param string $uuid The string to validate if it is in the proper UUID format.
     *
     * @param bool TRUE if the format is valid and FALSE otherwise.
     */
    public static function isValid($uuid)
    {
        return (preg_match('/^\{?[0-9a-f]{8}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?[0-9a-f]{12}\}?$/i', $uuid) === 1);
    }

    /**
     * Generate UUID
     *
     * @param int    $version UUID version
     * @param string $node    Node Name is IEEE 802 MAC address or pseudo-randomly generated.
     * @param string $ns      UUID namespace
     *
     * @return UUID
     *
     * @throws InvalidArgumentException
     */
    public static function generate($version = 1, $node = null, $ns = null)
    {
        switch ((int) $version) {
            case 1:
                return new self(static::mintTime($node));
            case 2:
                // Version 2 is not supported
                throw new InvalidArgumentException('UUID v2 is unsupported');
            case 3:
                return new self(static::mintName(static::MD5, $node, $ns));
            case 4:
                return new self(static::mintRand());
            case 5:
                return new self(static::mintName(static::SHA1, $node, $ns));
            default:
                throw new InvalidArgumentException(sprintf('UUID version (%d) is invalid or unsupported', $version));
        }
    }

    /**
     * Generates UUID.
     *
     * @param string $node
     *
     * @return string
     */
    public static function __callStatic($name, $arguments)
    {
        if ( ! in_array($name, ['v1', 'v3', 'v4', 'v5'])) {
            throw new BadMethodCallException(sprintf('Undefined method name: "%s"', $name));
        }

        $version = ltrim($name, 'v');

        array_unshift($arguments, $version);

        return call_user_func_array([__CLASS__, 'generate'], $arguments);
    }


    /**
     * Generates a Version 1 UUID.
     *
     * These are derived from the time at which they were generated.
     *
     * It uses the time since Gregorian calendar reform in 100ns intervals
     * which is is exceedingly difficult because of PHP's (and pack()'s)
     * integer size limits.
     *
     * Note: That this will never be more accurate than to the microsecond.
     *
     * @param string $node
     *
     * @return string
     */
    protected static function mintTime($node = null)
    {
        // Time since since Gregorian calendar reform in 100ns intervals
        $time = microtime(1) * 10000000 + static::INTERVAL;

        // Convert to a string representation
        $time = sprintf("%F", $time);

        // Strip decimal point
        preg_match("/^\d+/", $time, $time);

        // And now to a 64-bit binary representation
        $time = base_convert($time[0], 10, 16);
        $time = pack("H*", str_pad($time, 16, "0", STR_PAD_LEFT));

        // Reorder bytes to their proper locations in the UUID
        $uuid = $time[4] . $time[5] . $time[6] . $time[7] . $time[2] . $time[3] . $time[0] . $time[1];

        // Generate a random clock sequence
        $uuid .= static::randomBytes(2);

        // Set variant
        $uuid[8] = chr(ord($uuid[8]) & static::CLEAR_VAR | static::VAR_RFC);

        // Set version
        $uuid[6] = chr(ord($uuid[6]) & static::CLEAR_VER | static::VERSION_1);

        // Set the final 'node' parameter, a MAC address
        if ( ! is_null($node)) {
            $node = static::toBin($node, 6);
        }

        // If no node was provided or if the node was invalid,
        //  generate a random MAC address and set the multicast bit
        if (is_null($node)) {
            $node = static::randomBytes(6);
            $node[0] = pack("C", ord($node[0]) | 1);
        }

        $uuid .= $node;

        return $uuid;
    }

    /**
     * Randomness is returned as a string of bytes
     *
     * Generate a 16-byte string of random raw data.
     *
     * @param $length Number of bytes.
     *
     * @return string
     */
    public static function randomBytes($length)
    {
        return Random::bytes($length);
    }

    /**
     * Insure that an input string is either binary or hexadecimal.
     *
     * Returns binary representation, or false on failure.
     *
     * @param string  $str
     * @param integer $len
     *
     * @return string|null
     */
    protected static function toBin($str, $len)
    {
        if ($str instanceof self) {
            return $str->getBytes();
        }

        if (strlen($str) === $len) {
            return $str;
        }

        // Strip URN scheme and namespace
        $str = preg_replace('/^urn:uuid:/is', '', $str);

        // Strip non-hex characters
        $str = preg_replace('/[^a-f0-9]/is', '', $str);

        if (strlen($str) !== ($len * 2)) {
            return null;
        }

        return pack("H*", $str);
    }

    /**
     * Generates a Version 3 or Version 5 UUID.
     *
     * These are derived from a hash of a name and its namespace,
     * in binary form.
     *
     * @param string $ver
     * @param string $node
     * @param string $ns
     *
     * @return string
     *
     * @throws InvalidArgumentException
     */
    protected static function mintName($ver, $node, $ns)
    {
        if (empty($node)) {
            throw new InvalidArgumentException('A name-string is required for Version 3 or 5 UUIDs');
        }

        // If the namespace UUID isn't binary, make it so
        $ns = static::toBin($ns, 16);
        if (is_null($ns)) {
            throw new InvalidArgumentException('A binary namespace is required for Version 3 or 5 UUIDs');
        }

        $version = null;
        $uuid = null;

        switch ($ver) {
            case static::MD5:
                $version = static::VERSION_3;
                $uuid = md5($ns . $node, 1);
                break;
            case static::SHA1:
                $version = static::VERSION_5;
                $uuid = substr(sha1($ns . $node, 1), 0, 16);
                break;
            default:
                // no default really required here
        }

        // set variant
        $uuid[8] = chr(ord($uuid[8]) & static::CLEAR_VAR | static::VAR_RFC);

        // set version
        $uuid[6] = chr(ord($uuid[6]) & static::CLEAR_VER | $version);

        return ($uuid);
    }

    /**
     * Generate a Version 4 UUID.
     *
     * These are derived solely from random numbers.
     * generate random fields
     *
     * @return string
     */
    protected static function mintRand()
    {
        $uuid = static::randomBytes(16);

        // Set variant
        $uuid[8] = chr(ord($uuid[8]) & static::CLEAR_VAR | static::VAR_RFC);

        // Set version
        $uuid[6] = chr(ord($uuid[6]) & static::CLEAR_VER | static::VERSION_4);

        return $uuid;
    }

    /**
     * Import an existing UUID
     *
     * @param string $uuid
     *
     * @return UUID
     */
    public static function import($uuid)
    {
        return new self(static::toBin($uuid, 16));
    }

    /**
     * Compares the binary representations of two UUIDs.
     *
     * The comparison will return true if they are bit-exact,
     * or if neither is valid.
     *
     * @param string $a
     * @param string $b
     *
     * @return string|string
     */
    public static function compare($a, $b)
    {
        return (static::toBin($a, 16) == static::toBin($b, 16));
    }

    /**
     * Get UUID Bytes
     *
     * @param integer $i
     *
     * @return string
     */
    public function getBytes($i = null)
    {
        return (is_null($i) ? $this->bytes : $this->bytes[$i]);
    }

    /**
     * Get UUID Bytes as Hex
     *
     * @return string
     */
    public function getHex()
    {
        return bin2hex($this->getBytes());
    }

    /**
     * Get UUID Value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Get UUID Version
     *
     * @return integer
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Get UUID Variant
     *
     * @return integer
     */
    public function getVariant()
    {
        $byte = ord($this->getBytes(8));

        if ($byte >= static::VAR_RES) {
            return 3;
        } elseif ($byte >= static::VAR_MS) {
            return 2;
        } elseif ($byte >= static::VAR_RFC) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * Get UUID Node
     *
     * @return integer
     */
    public function getNode()
    {
        if ($this->getVersion() !== 1) {
            return null;
        }

        return bin2hex(substr($this->getBytes(), 10));
    }

    /**
     * Get UUID Time
     *
     * @return integer
     */
    public function getTime()
    {
        if ($this->getVersion() !== 1) {
            return null;
        }

        // Restore contiguous big-endian byte order
        $bytes = $this->getBytes();
        $bin = $bytes[6] . $bytes[7] . $bytes[4] . $bytes[5] . $bytes[0] . $bytes[1] . $bytes[2] . $bytes[3];

        $time = bin2hex($bin);

        // Clear version flag
        $time[0] = "0";

        // Do some reverse arithmetic to get a Unix timestamp
        return (hexdec($time) - static::INTERVAL) / 10000000;
    }

    /**
     * Get UUID with Uniform Resource Names (URN) Namespace
     *
     * @return string
     */
    public function getURN()
    {
        return sprintf('urn:uuid:%s', $this->getValue());
    }

    /**
     * Return the UUID
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getValue();
    }
}
