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
 * OpenSSL random data generator
 *
 * This generator provides an interface to the openssl extension. On most
 * platforms, the extension will use the CSPRNG provided by the OpenSSL library.
 * Due to a bug in the extension, this generator is unavailable on PHP
 * versions < 5.3.7 on Windows platforms. These buggy versions attempted to
 * gather additional entropy from an attached display device. While this worked
 * fine on workstations, this would cause headless servers to run very slowly
 * or hang.
 *
 * The behavior of the openssl extension on Windows was modified further
 * starting with PHP 5.4.0 to bypass the OpenSSL CSPRNG completely and use
 * Windows' built-in CSPRNG instead via Microsoft's CryptoAPI.
 */
class OpenSSLGenerator implements GeneratorInterface
{
    /**
     * Generate a string of random data.
     *
     * Pad with chr(0) since that's what mcrypt_generic does to
     * make sure the length of the data is n * blocksize.
     *
     * @see http://php.net/mcrypt_generic
     *
     * @param integer $byteCount The desired number of bytes.
     *
     * @return string  Returns the generated string.
     */
    public function generate($byteCount)
    {
        $bytes = '';

        if (self::isSupported()) {
            $cryptoStrong = false;
            $openSSLBytes = openssl_random_pseudo_bytes($byteCount, $cryptoStrong);
            if ($cryptoStrong) {
                $bytes = $openSSLBytes;
            }
            unset($cryptoStrong);
        }

        return str_pad($bytes, $byteCount, chr(0));
    }

    /**
     * Test system support for this generator.
     *
     * PHP versions prior to 5.3.7 have a bug in the Windows implementation of
     * this generator. The implementation used OpenSSL functions which could
     * cause blocking for an indefinite period of time on headless
     * non-interactive Windows servers. Because of this, the generator is not
     * supported for PHP versions < 5.3.7 on Windows.
     *
     * The OpenSSL function this generator uses simply wraps Microsoft's
     * CryptoAPI on PHP versions >= 5.4.0 on Windows. It should be noted that
     * this is also exactly how the MCrypt generator operates on Windows.
     *
     * @return boolean Returns true if the generator is supported on the current
     *     platform, otherwise false.
     */
    public static function isSupported()
    {
        $supported = false;
        if (function_exists('openssl_random_pseudo_bytes')) {
            if (version_compare(PHP_VERSION, '5.3.7') >= 0 || (PHP_OS & "\xDF\xDF\xDF") !== 'WIN') {
                $supported = true;
            }
        }

        return $supported;
    }

    /**
     * Get the generator priority.
     *
     * @return integer Returns an integer indicating the priority of the
     *     generator. Lower numbers represent lower priorities.
     */
    public static function getPriority()
    {
        return GeneratorInterface::PRIORITY_HIGH;
    }
}