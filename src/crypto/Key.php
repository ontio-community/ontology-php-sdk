<?php

namespace ontio\crypto;

use ontio\common\ByteArray;
use ontio\sdk\Constant;
use \JsonSerializable;

class Key implements JsonSerializable
{
  /**
   * @var KeyType
   */
  public $algorithm;

  /**
   * @var KeyParameters
   */
  public $parameters;

  /**
   * @var ByteArray
   */
  public $key;

  public function __construct(ByteArray $key, ? KeyType $algorithm = null, ? KeyParameters $parameters = null)
  {
    $this->key = $key;

    if ($algorithm === null)
      $algorithm = KeyType::fromLabel(Constant::$DEFAULT_ALGORITHM->get('algorithm'));

    if ($parameters === null)
      $parameters = KeyParameters::fromCurve(Constant::$DEFAULT_ALGORITHM->get('parameters.curve'));

    $this->algorithm = $algorithm;
    $this->parameters = $parameters;
  }

  /**
   * Computes hash of message using hashing function of signature schema.
   *
   * @param ByteArray $msg msg to be encoded
   * @param SignatureScheme $scheme Signing scheme to use
   * @return string the binary string containing hash data
   */
  public function computeHash(ByteArray $msg, SignatureScheme $scheme) : string
  {
    $algo = null;
    switch ($scheme->value) {
      case SignatureScheme::$EcdsaWithSha224->value:
        $algo = 'sha224';
        break;
      case SignatureScheme::$EcdsaWithSha256->value:
        $algo = 'sha256';
        break;
      case SignatureScheme::$EcdsaWithSha384->value:
        $algo = 'sha384';
        break;
      case SignatureScheme::$EcdsaWithSha512->value:
      case SignatureScheme::$EddsaWithSha512->value:
        $algo = 'sha512';
        break;
      case SignatureScheme::$EcdsaWithSha3_224->value:
        $algo = 'sha3-224';
        break;
      case SignatureScheme::$EcdsaWithSha3_256->value:
        $algo = 'sha3-256';
        break;
      case SignatureScheme::$EcdsaWithSha3_384->value:
        $algo = 'sha3-384';
        break;
      case SignatureScheme::$EcdsaWithSha3_512->value:
        $algo = 'sha3-512';
        break;
      case SignatureScheme::$EcdsaWithRipemd160->value:
        $algo = 'ripemd160';
        break;
      case SignatureScheme::$Sm2WithSm3->value:
        return openssl_digest($msg->toBinary(), 'sm3');
      default:
        throw new \InvalidArgumentException("Unsupported hash algorithm");
    }

    return hash($algo, $msg->toBinary());
  }

  public function isSchemeSupported(SignatureScheme $scheme) : bool
  {
    switch ($scheme->value) {
      case SignatureScheme::$EcdsaWithSha224->value:
      case SignatureScheme::$EcdsaWithSha256->value:
      case SignatureScheme::$EcdsaWithSha384->value:
      case SignatureScheme::$EcdsaWithSha512->value:
      case SignatureScheme::$EcdsaWithSha3_224->value:
      case SignatureScheme::$EcdsaWithSha3_256->value:
      case SignatureScheme::$EcdsaWithSha3_384->value:
      case SignatureScheme::$EcdsaWithSha3_512->value:
      case SignatureScheme::$EcdsaWithRipemd160->value:
        return $this->algorithm === KeyType::$Ecdsa;
      case SignatureScheme::$EddsaWithSha512->value:
        return $this->algorithm === KeyType::$Eddsa;
      case SignatureScheme::$Sm2WithSm3->value:
        return $this->algorithm === KeyType::$Sm2;
      default:
        throw new \InvalidArgumentException("Unsupported hash scheme");
    }
  }

  public function jsonSerialize()
  {
    return [
      'algorithm' => $this->algorithm->label,
      'parameters' => $this->parameters->jsonSerialize(),
      'key' => $this->key->toHex()
    ];
  }
}
