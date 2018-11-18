<?php

namespace ontio\sdk;

use ontio\crypto\PrivateKey;
use ontio\crypto\Address;
use ontio\common\ByteArray;
use ontio\crypto\ScryptParams;
use \JsonSerializable;
use ontio\core\transaction\Transaction;
use ontio\core\transaction\TransactionBuilder;

class Identity implements JsonSerializable
{
  /** @var string */
  public $ontid;

  /** @var string */
  public $label;

  /** @var bool */
  public $lock;

  /** @var bool */
  public $isDefault;

  /** @var ControlData[] */
  public $controls = [];

  public $extra;

  /**
   * Import identity
   *
   * @param string $label Name of identity
   * @param PrivateKey $enPriKey Encrypted private key
   * @param string $pwd password to decrypt
   * @param Address $addr Address to decrypt
   * @param string $salt64 Salt to decrypt
   * @param ScryptParams|null $params Optional params to decrypt
   * @return self
   */
  public static function import(
    string $label,
    PrivateKey $enPriKey,
    string $pwd,
    Address $addr,
    string $salt64,
    ? ScryptParams $params = null
  ) : self {
    $id = new self();
    $salt = ByteArray::fromBase64($salt64)->toBinary();
    $priKey = $enPriKey->decrypt($pwd, $addr, $salt, $params);

    if (empty($label)) {
      $label = ByteArray::random(4)->toHex();
    }

    $pubKey = $priKey->getPublicKey();
    $id->ontid = Address::generateOntid($pubKey);
    $id->label = $label;
    $id->lock = false;
    $id->isDefault = false;

    $ctrl = new ControlData('1', $enPriKey, Address::fromOntId($id->ontid), $salt64);
    $ctrl->publicKey = $pubKey->toHex();
    $id->controls[] = $ctrl;

    return $id;
  }

  public static function create(PrivateKey $prikey, string $pwd, string $label, ? ScryptParams $params = null) : self
  {
    $id = new self();
    $id->ontid = '';
    $id->label = $label;
    $id->lock = false;
    $id->isDefault = false;

    $pubKey = $prikey->getPublicKey();
    $id->ontid = Address::generateOntid($pubKey);

    $addr = Address::fromOntId($id->ontid);
    $salt = ByteArray::random(16);
    $encPrikey = $prikey->encrypt($pwd, $addr, $salt->toBinary(), $params);

    $salt64 = $salt->toBase64();
    $ctrl = new ControlData('1', $encPrikey, $addr, $salt64);
    $ctrl->publicKey = $pubKey->toHex();
    $id->controls[] = $ctrl;

    return $id;
  }

  public static function importFromKeystore(Keystore $store, string $password) : self
  {
    if ($store->type !== 'I') {
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

  public static function fromJson(string $json) : self
  {
    return self::fromJsonObj(json_decode($json));
  }

  public static function fromJsonObj($obj) : self
  {
    $id = new self();
    $id->ontid = $obj->ontid;
    $id->label = $obj->label;
    $id->lock = $obj->lock;
    $id->isDefault = $obj->isDefault;
    $id->controls = array_map(function ($c) {
      return ControlData::fromJsonObj($c);
    }, $obj->controls);
    $id->extra = $obj->extra;
    return $id;
  }

  public function jsonSerialize()
  {
    $controls = array_map(function ($c) {
      /** @var ControlData $c */
      return $c->jsonSerialize();
    }, $this->controls);

    return [
      'ontid' => $this->ontid,
      'label' => $this->label,
      'lock' => $this->lock,
      'isDefault' => $this->isDefault,
      'controls' => $controls,
      'extra' => $this->extra
    ];
  }

  public function exportPrivateKey(string $pwd, ? ScryptParams $params = null) : Privatekey
  {
    $encPrikey = $this->controls[0]->encryptedKey;
    $addr = $this->controls[0]->address;
    $salt = $this->controls[0]->salt;
    return $encPrikey->decrypt($pwd, $addr, ByteArray::fromBase64($salt)->toBinary(), $params);
  }

  public function signTransaction(string $pwd, Transaction $tx, ? ScryptParams $params = null) : Transaction
  {
    $prikey = $this->exportPrivateKey($pwd, $params);
    $builder = new TransactionBuilder();
    $builder->signTransaction($tx, $prikey, $prikey->algorithm->defaultScheme);
    return $tx;
  }

  public function exportKeystore() : Keystore
  {
    $ks = new Keystore();
    $ctrl = $this->controls[0];
    $ks->type = 'I';
    $ks->label = $this->label;
    $ks->algorithm = $ctrl->encryptedKey->algorithm->label;
    $ks->scrypt = $ctrl->encryptedKey->scrypt;
    $ks->key = $ctrl->encryptedKey->key->toBase64();
    $ks->salt = $ctrl->salt;
    $ks->address = $ctrl->address;
    $ks->parameters = $ctrl->encryptedKey->parameters;
    return $ks;
  }
}
