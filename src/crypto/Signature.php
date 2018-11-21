<?php

namespace ontio\crypto;

use ontio\common\ByteArray;


class Signature
{
  /**
   * @var SignatureScheme
   */
  public $algorithm;

  /**
   * @var ByteArray
   */
  public $value;

  /**
   * @var ?string
   */
  public $publicKeyId;

  public function __construct(ByteArray $value, SignatureScheme $scheme, ? string $publicKeyId = null)
  {
    $this->value = $value;
    $this->algorithm = $scheme;
    $this->publicKeyId = $publicKeyId;
  }

  /**
   * @param string $encoded hex string
   * @param SignatureScheme $algorithm
   * @param string $publicKeyId
   * @return Signature
   */
  public static function fromJwt(string $encoded, SignatureScheme $algorithm, string $publicKeyId) : Signature
  {
    $dec = base64_decode(ByteArray::fromHex(encoded)->toBinary());
    return new self(ByteArray::fromBinary($dec), $algorithm, $publicKeyId);
  }

  /**
   * @param string $data hex string
   * @return Signature
   */
  public static function fromHex(string $data) : Signature
  {
    $ba = ByteArray::fromHex($data);
    if ($ba->length() < 4) throw new \InvalidArgumentException("Deformed input");

    $schemeVal = $ba->readUInt8(0);
    $scheme = SignatureScheme::fromValue($schemeVal);
    $value = $ba->slice(1);
    return new self($value, $scheme);
  }

  public function toHex() : string
  {
    $ba = new ByteArray([$this->algorithm->value]);
    $ba->pushArray($this->value);
    return $ba->toHex();
  }
}
