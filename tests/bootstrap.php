<?php
/*
 * This file is part of the Crypto package.
 *
 * (c) Unit6 <team@unit6websites.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// set the default timezone
date_default_timezone_set('UTC');


/**
 * Random MAC Address
 *
 * @example '32:F1:39:2F:D6:18'
 *
 * @return string
 */
function macAddress()
{
    $between = function ($int1 = 0, $int2 = 2147483647) {
        $min = $int1 < $int2 ? $int1 : $int2;
        $max = $int1 < $int2 ? $int2 : $int1;
        return mt_rand($min, $max);
    };

    for ($i=0; $i<6; $i++) {
        $mac[] = sprintf('%02X', $between(0, 0xff));
    }

    $mac = implode(':', $mac);

    return $mac;
}

require realpath(__DIR__ . '/../vendor/autoload.php');