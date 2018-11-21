<?php

namespace ontio\crypto;

use ontio\common\ByteArray;
use ontio\sdk\Constant;

class Scrypt
{
  /**
   * @param string $password
   * @param string $salt
   * @param ScryptParams $params
   * @return string
   */
  public static function enc(string $password, string $salt, ScryptParams $params) : string
  {
    return scrypt($password, $salt, $params->n, $params->r, $params->p, $params->dkLen);
  }

  /**
   * @param string $priKey
   * @param string $address58
   * @param string $salt
   * @param string $password
   * @return string
   * @throws \Exception
   */
  static function encryptWithGcm(string $priKey, string $address58, string $salt, string $password, ? ScryptParams $params = null) : string
  {
    if ($params === null) {
      $params = ScryptParams::$default;
    }
    $derived = Scrypt::enc($password, $salt, $params);

    $derivedBytes = ByteArray::fromHex($derived);
    $iv = $derivedBytes->slice(0, 12)->toBinary();
    $key = $derivedBytes->slice(32)->toBinary();

    $cipherText = openssl_encrypt($priKey, "aes-256-gcm", $key, OPENSSL_RAW_DATA, $iv, $tag, $address58);
    return base64_encode($cipherText . $tag);
  }

  /**
   * @param string $encrypted
   * @param string $address58
   * @param string $salt
   * @param string $password
   * @return string|false
   * @throws \Exception
   */
  static function decryptWithGcm(string $encrypted, string $address58, string $salt, string $password, ? ScryptParams $params = null) : string
  {
    if ($params === null) {
      $params = ScryptParams::$default;
    }
    $derived = Scrypt::enc($password, $salt, $params);

    $derivedBytes = ByteArray::fromHex($derived);
    $iv = $derivedBytes->slice(0, 12)->toBinary();
    $key = $derivedBytes->slice(32)->toBinary();

    $bytes = ByteArray::fromBinary($encrypted);
    $cipherBytes = $bytes->slice(0, $bytes->length() - 16);;
    $tagBytes = $bytes->slice($bytes->length() - 16);

    $cipherText = pack("C*", ...$cipherBytes->bytes);
    $tag = pack("C*", ...$tagBytes->bytes);
    return openssl_decrypt($cipherText, "aes-256-gcm", $key, OPENSSL_RAW_DATA, $iv, $tag, $address58);
  }
}
