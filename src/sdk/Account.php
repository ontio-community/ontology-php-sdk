<?php

namespace ontio\sdk;

use ontio\crypto\PrivateKey;
use ontio\crypto\ScryptParams;
use ontio\crypto\HDKeyFactory;
use ontio\common\ByteArray;
use ontio\crypto\Address;
use \JsonSerializable;

class Account implements JsonSerializable
{
  /**
   * @var string
   */
  public $label;

  /**
   * @var Address
   */
  public $address;

  /**
   * @var bool
   */
  public $lock;

  /**
   * @var PrivateKey
   */
  public $encryptedKey;

  /**
   * @var string
   */
  public $hash;

  /**
   * @var string
   */
  public $salt;

  /**
   * @var string
   */
  public $publicKey;

  /**
   * @var bool
   */
  public $isDefault;

  public $extra;

  public static function create(
    string $password,
    PrivateKey $priKey = null,
    string $label = '',
    ScryptParams $params = null
  ) : self {
    $acc = new self();

    if ($priKey === null) {
      $priKey = PrivateKey::random();
    }

    if ($label === '') {
      $label = ByteArray::random(4);
      $label = $label->toHex();
    }

    $acc->label = $label;
    $acc->lock = false;
    $acc->isDefault = false;

    $salt = ByteArray::random(16);
    $pubKey = $priKey->getPublicKey();
    $addr = Address::fromPubKey($pubKey);

    $acc->publicKey = $pubKey->toHex();
    $acc->address = $addr;
    $acc->encryptedKey = $priKey->encrypt($password, $addr, $salt->toBinary(), $params);
    $acc->salt = $salt->toBase64();
    return $acc;
  }

  public static function import(
    string $label,
    PrivateKey $encPriKey,
    string $password,
    Address $address,
    string $salt,
    ScryptParams $params = null
  ) : self {
    $acc = new self();
    $salt = ByteArray::fromBase64($salt);
    $pk = $encPriKey->decrypt($password, $address, $salt->toBinary(), $params);

    if (!$label) {
      $label = ByteArray::random(4);
      $label = $label->toHex();
    }

    $acc->label = $label;
    $acc->lock = false;
    $acc->isDefault = false;
    $acc->salt = $salt->toBase64();
    $acc->encryptedKey = $encPriKey;

    $pub = $pk->getPublicKey();
    $acc->publicKey = $pub->toHex();
    $acc->address = Address::fromPubKey($pub);
    return $acc;
  }

  public static function importFromKeystore(Keystore $store, string $password) : self
  {
    if ($store->type !== 'A') {
      throw new \InvalidArgumentException('deformed type: ' . $store->type);
    }

    $encKey = PrivateKey::fromJsonObj((object)[
      'algorithm' => $store->algorithm,
      'parameters' => (object)['curve' => $store->parameters->curve->label],
      'key' => $store->key,
      'scrypt' => $store->scrypt,
      'external' => null
    ]);

    return self::import(
      $store->label,
      $encKey,
      $password,
      $store->address,
      $store->salt
    );
  }

  public static function importFromWif(
    string $password,
    string $wif,
    string $label = '',
    ScryptParams $params = null
  ) : self {
    $prikey = PrivateKey::fromWif($wif);
    return self::create($params, $priKey, $label, $params);
  }

  public static function importFromMnemonic(
    string $mnemonic,
    string $label = '',
    string $password = '',
    ? ScryptParams $params = null
  ) : self {
    $pk = PrivateKey::generateFromMnemonic($mnemonic);
    return self::create($password, $pk, $label, $params);
  }

  public function exportPrivateKey(string $password = '', ScryptParams $params = null) : PrivateKey
  {
    $salt = ByteArray::fromBase64($this->salt)->toBinary();
    return $this->encryptedKey->decrypt($password, $this->address, $salt, $params);
  }

  public function jsonSerialize()
  {
    $self = [
      'address' => $this->address->toBase58(),
      'label' => $this->label,
      'lock' => $this->lock,
      'enc-alg' => 'aes-256-gcm',
      'hash' => $this->hash,
      'salt' => $this->salt,
      'isDefault' => $this->isDefault,
      'publicKey' => $this->publicKey,
      'signatureScheme' => $this->encryptedKey->algorithm->defaultScheme->label
    ];
    return array_merge($self, $this->encryptedKey->jsonSerialize());
  }

  public static function fromJsonObj($obj) : self
  {
    $acc = new self();
    $acc->address = new Address($obj->address);
    $acc->label = $obj->label;
    $acc->lock = $obj->lock;
    $acc->isDefault = $obj->isDefault;
    $acc->publicKey = $obj->publicKey;
    $acc->hash = $obj->hash;
    $acc->salt = $obj->salt;
    $acc->encryptedKey = PrivateKey::fromJsonObj((object)[
      'algorithm' => $obj->algorithm,
      'parameters' => $obj->parameters,
      'key' => $obj->key,
      'external' => $obj->external
    ]);
    $acc->extra = $obj->extra;
    return $acc;
  }

  public function exportKeystore() : Keystore
  {
    $ks = new Keystore();
    $ks->type = 'A';
    $ks->label = $this->label;
    $ks->algorithm = $this->encryptedKey->algorithm->label;
    $ks->scrypt = $this->encryptedKey->scrypt;
    $ks->key = $this->encryptedKey->key->toBase64();
    $ks->salt = $this->salt;
    $ks->address = $this->address;
    $ks->parameters = $this->encryptedKey->parameters;
    return $ks;
  }
}
