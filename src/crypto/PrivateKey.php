<?php

namespace ontio\crypto;

use ontio\Constant;
use ontio\common\ByteArray;
use Elliptic\EC;
use Elliptic\EC\Signature as EcSignature;
use Elliptic\EdDSA;
use Elliptic\EC\KeyPair;
use ontio\ErrorCode;
use ontio\common\Util;
use BitWasp\Bitcoin\Base58;
use BitWasp\Buffertools\Buffer;
use BitWasp\Bitcoin\Mnemonic\Bip39\Bip39SeedGenerator;
use BitWasp\Bitcoin\Bitcoin;
use Mdanter\Ecc\EccFactory;

class PrivateKey extends Key
{
  public static function random(? KeyType $keyType = null, ? KeyParameters $keyParameters = null) : self
  {
    return new self(ByteArray::random(32), $keyType, $keyParameters);
  }

  public static function fromJsonObj($obj) : self
  {
    return new self(
      ByteArray::fromBase64($obj->key),
      KeyType::fromLabel($obj->algorithm),
      KeyParameters::fromJsonObj($obj->parameters)
    );
  }

  public static function generateFromMnemonic(string $mnemonic) : self
  {
    $mnemonic = trim($mnemonic);
    $seed = (new Bip39SeedGenerator())->getSeed($mnemonic);
    $adapter = Bitcoin::getEcAdapter(null, EccFactory::getSecgCurves()->generator256r1());
    $factory = new HDKeyFactory($adapter);
    $root = $factory->fromEntropy($seed);
    $purposePriKey = $root->derivePath(Constant::$ONT_BIP44_PATH)->getPrivateKey()->getHex();
    return new PrivateKey(ByteArray::fromHex($purposePriKey));
  }

  public function sign(ByteArray $msg, ? SignatureScheme $scheme = null, ? string $publicKeyId = null) : Signature
  {
    if ($scheme === null) $scheme = $this->algorithm->defaultScheme;
    $this->isSchemeSupported($scheme);

    $hash;
    if ($scheme === SignatureScheme::$Sm2WithSm3) {
      $hash = $msg->toBinary();
    } else {
      $hash = $this->computeHash($msg, $scheme);
    }
    $signed = $this->computeSignature($hash, $scheme);
    return new Signature(ByteArray::fromHex($signed), $scheme, $publicKeyId);
  }

  /**
   * Computes signature of message hash using specified signature schema.
   *
   * @param string $hash the binary string of hash
   * @param SignatureScheme $scheme Signature schema to use
   * @return string the binary string containing signature data
   */
  public function computeSignature(string $hash, SignatureScheme $scheme) : string
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
        return $this->computeEcdsaSignature($hash);
      case SignatureScheme::$EddsaWithSha512->value:
        return $this->computeEddsaSignature($hash);
      case SignatureScheme::$Sm2WithSm3->value:
        return $this->computeSm2Signature($hash);
      default:
        throw new Error('Unsupported signature schema.');
    }
  }

  public function computeEcdsaSignature(string $hash) : string
  {
    $ec = new EC($this->parameters->curve->preset);
    /** @var EcSignature */
    $signed = $ec->sign($hash, $this->key->toHex(), ['canonical' => true]);
    $r = $signed->r->toArray('be', 32);
    $s = $signed->s->toArray('be', 32);
    return (new ByteArray($r))->push(...$s)->toHex();
  }

  public function computeEddsaSignature(string $hash) : string
  {
    $ed = new EdDSA($this->parameters->curve->preset);
    $signed = $ed->sign($hash, $this->key->toHex());
    $r = $signed->r->toArray('be', 32);
    $s = $signed->s->toArray('be', 32);
    return (new ByteArray($r))->push(...$s)->toHex();
  }

  public function computeSm2Signature($msg) : string
  {
    $pk = sm2_pkey_from_pri($this->key->toHex());
    $signed = sm2_sign($msg, $pk);
    return ByteArray::fromBinary(Constant::$DEFAULT_SM2_ID)->push(0)->toHex() . $signed;
  }

  public function getPublicKey() : PublicKey
  {
    switch ($this->algorithm->value) {
      case KeyType::$Ecdsa->value:
        return $this->getEcdsaPublicKey();
      case KeyType::$Eddsa->value:
        return $this->getEddsaPublicKey();
      case KeyType::$Sm2->value:
        return $this->getSm2PublicKey();
      default:
        throw new \InvalidArgumentException('Unsupported signature schema.');
    }
  }

  public function getEcdsaPublicKey() : PublicKey
  {
    $ec = new EC($this->parameters->curve->preset);
    /** @var KeyPair */
    $keyPair = $ec->keyFromPrivate($this->key->toHex(), 'hex');
    $pk = $keyPair->getPublic(true, 'hex');
    return new PublicKey(ByteArray::fromHex($pk), $this->algorithm, $this->parameters);
  }

  public function getEddsaPublicKey() : PublicKey
  {
    $ed = new EdDSA($this->parameters->curve->preset);
    $keyPair = $ed->keyFromSecret($this->key->toHex(), 'hex');
    $pk = $keyPair->getPublic('hex');
    return new PublicKey(ByteArray::fromHex($pk), $this->algorithm, $this->parameters);
  }

  public function getSm2PublicKey() : PublicKey
  {
    $pk = sm2_pkey_from_pri($this->key->toHex());
    $key = sm2_pkey_get_public($pk, "compress", true);
    return new PublicKey(ByteArray::fromHex($key), $this->algorithm, $this->parameters);
  }

  public function encrypt(string $keyPhrase, Address $address, string $salt, ? ScryptParams $params = null) : self
  {
    $pub = $this->getPublicKey();
    $addr = Address::fromPubKey($pub)->toBase58();
    if ($addr !== $address->toBase58()) {
      throw new \InvalidArgumentException(ErrorCode::INVALID_ADDR);
    }
    $enc = Scrypt::encryptWithGcm($this->key->toBinary(), $addr, $salt, $keyPhrase, $params);
    return new self(ByteArray::fromBase64($enc), $this->algorithm, $this->parameters);
  }

  public function decrypt(string $keyPhrase, Address $address, string $salt, ? ScryptParams $params = null) : self
  {
    try {
      $dec = Scrypt::decryptWithGcm($this->key->toBinary(), $address->toBase58(), $salt, $keyPhrase, $params);
      $key = new self(ByteArray::fromBinary($dec), $this->algorithm, $this->parameters);
      $pub = $key->getPublicKey();
      $addr = Address::fromPubKey($pub)->toBase58();
      if ($addr !== $address->toBase58()) throw new \Exception();
    } catch (\Exception $e) {
      throw new \InvalidArgumentException(ErrorCode::DECRYPT_ERROR);
    }
    return $key;
  }

  public function toWif() : string
  {
    $data = array_fill(0, 38, 0);
    $data[0] = 0x80;

    Util::arrayCopy($this->key->bytes, 0, $data, 1, 32);

    $data[33] = 0x01;

    $stuff = array_slice($data, 0, 34);
    $checksum = hash("sha256", hash("sha256", ByteArray::fromBytes($stuff)->toBinary(), true), true);

    Util::arrayCopy(ByteArray::fromBinary($checksum)->bytes, 0, $data, 34, 4);
    return Base58::encode(Buffer::hex(ByteArray::fromBytes($data)->toHex()));
  }

  public static function fromWif(string $wif) : self
  {
    $data = ByteArray::fromHex(Base58::decode($wif)->getHex());
    $bytes = $data->bytes;
    if ($data->length() !== 38 || $bytes[0] !== 0x80 || $bytes[33] !== 0x01) {
      throw new \InvalidArgumentException('deformed wif');
    }
    $stuff = array_slice($bytes, 0, 34);
    $checksum = hash("sha256", hash("sha256", ByteArray::fromBytes($stuff)->toBinary(), true), true);
    $chkBytes = ByteArray::fromBinary($checksum)->bytes;

    for ($i = 0; $i < 4; $i++) {
      if ($bytes[34 + $i] != $chkBytes[$i]) {
        throw new \InvalidArgumentException('illegal wif');
      }
    }

    $pkBytes = array_fill(0, 32, 0);
    Util::arrayCopy($bytes, 1, $pkBytes, 0, 32);
    return new PrivateKey(ByteArray::fromBytes($pkBytes));
  }

  public function jsonSerialize()
  {
    return [
      'algorithm' => $this->algorithm->label,
      'parameters' => $this->parameters->jsonSerialize(),
      'key' => $this->key->toBase64()
    ];
  }
}
