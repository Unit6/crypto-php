# Unit6/Crypto

Simple crypto utility library.

### Example

```php
use Unit6\Crypto;

$uuid = Crypto\UUID::v4();
$nonce = Crypto\Random::getString(16);
```

### Requirements

Following required dependencies:

- PHP 5.6.x

### License

This project is licensed under the MIT license -- see the `LICENSE.txt` for the full license details.

### Acknowledgements

Some inspiration has been taken from the following projects:

- PRNG:
    - [ircmaxell/RandomLib](https://github.com/ircmaxell/RandomLib)
    - [phpseclib/phpseclib](https://github.com/phpseclib/phpseclib)
    - [rchouinard/rych-random](https://github.com/rchouinard/rych-random)
- UUID:
    - [lootils/uuid](https://github.com/lootils/uuid)
    - [ramsey/uuid](https://github.com/ramsey/uuid)
    - [webpatser/laravel-uuid](https://github.com/webpatser/laravel-uuid)
- NONCE:
    - [pafelin/laravel4-nonces](https://github.com/pafelin/laravel4-nonces)