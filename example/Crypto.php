<?php
/*
 * This file is part of the Crypto package.
 *
 * (c) Unit6 <team@unit6websites.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require realpath(dirname(__FILE__) . '/../autoload.php');
require realpath(dirname(__FILE__) . '/../vendor/autoload.php');

use Unit6\Crypto;

$random = new Crypto\Random();

// Generate a 16-byte string of random raw data
$randomBytes = Crypto\Random::bytes(16);
$randomBytesInHex = bin2hex($randomBytes);

// Get a random integer between 1 and 100
$randomInt = Crypto\Random::int(1, 100);

// Get a random 8-character string using the character set A-Za-z0-9./
$randomStr = Crypto\Random::str(8);

$uuid = Crypto\UUID::v4();

var_dump($uuid->getValue(), $randomStr, $randomInt, $randomBytesInHex); exit;