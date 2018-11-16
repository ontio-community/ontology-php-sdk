<?php

namespace ontio\crypto;

use ontio\common\ForwardBuffer;
use ontio\common\ByteArray;
use Elliptic\EC;
use Elliptic\EdDSA;
use ontio\sdk\Constant;

class PublicKey extends Key
{
  public static function fromHex(ForwardBuffer $fb, int $len = 33) : self
  {
    if ($len === 33) {
      $algo = KeyType::$Ecdsa;
      $curve = CurveLabel::$Secp256r1;
      $pk = $fb->forward(33);
      return new self($pk, $algo, new KeyParameters($curve));
    }

    $algo = $fb->readUInt8();
    $curve = $fb->readUInt8();
    $pk = $fb->forward($len - 2);
    return new self($pk, KeyType::fromValue($algo), new KeyParameters(CurveLabel::fromValue($curve)));
  }

  public function verify(ByteArray $msg, Signature $sig) : bool
  {
    if (!$this->isSchemeSupported($sig->algorithm)) {
      throw new \InvalidArgumentException('Signature schema does not match key type.');
    }

    $hash;
    if ($sig->algorithm === SignatureScheme::$Sm2WithSm3) {
      $hash = $msg->toBinary();
    } else {
      $hash = $this->computeHash($msg);
    }

    return $this->verifySignature($hash, $sig->value->toHex(), $sig->algorithm);
  }

  public function verifySignature(string $hash, string $sig, SignatureScheme $scheme) : bool
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
        return $this->verifyEcdsaSignature($hash, $sig);
      case SignatureScheme::$EddsaWithSha512->value:
        return $this->verifyEddsaSignature($hash, $sig);
      case SignatureScheme::$Sm2WithSm3->value:
        return $this->verifySm2Signature($hash, $sig);
      default:
        throw new Error('Unsupported signature schema.');
    }
  }

  public function verifyEcdsaSignature(string $hash, string $sig) : bool
  {
    $r = substr($sig, 0, 64);
    $s = substr($sig, 64, 64);
    $ec = new EC($this->parameters->curve->preset);
    return $ec->verify($hash, ['r' => $r, 's' => $s], $this->key->toBinary(), 'hex');
  }

  public function verifyEddsaSignature(string $hash, string $sig) : bool
  {
    $r = substr($sig, 0, 64);
    $s = substr($sig, 64, 64);
    $ed = new EdDSA($this->parameters->curve->preset);
    return $ed->verify($hash, ['r' => $r, 's' => $s], $this->key->toBinary(), 'hex');
  }

  public function verifySm2Signature(string $hash, string $sig) : bool
  {
    $expectId = ByteArray::fromBinary(Constant::$DEFAULT_SM2_ID);
    $sig = ByteArray::fromHex($sig);

    $id = $sig->slice(0, $expectId->length());

    if ($id->bytes !== $expectId->bytes) {
      throw new \InvalidArgumentException('Unsupported SM2 id used.');
    }

    $pk = sm2_pkey_from_pub($this->key->toHex());
    $sig = $sig->slice($expectId->length() + 1);
    return sm2_verify($hash, $sig->toHex(), $pk);
  }

  public function toHex() : string
  {
    $ret = new ByteArray([]);
    switch ($this->algorithm->value) {
      case KeyType::$Ecdsa->value:
        $ret = $this->key;
        break;
      case KeyType::$Eddsa->value:
      case KeyType::$Sm2->value:
        $ret->push($this->algorithm->value);
        $ret->push($this->parameters->curve->value);
        $ret->pushArray($this->key);
        break;
    }
    return $ret->toHex();
  }
}
