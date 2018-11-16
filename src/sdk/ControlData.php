<?php

namespace ontio\sdk;

use ontio\crypto\PrivateKey;
use ontio\crypto\Address;
use \JsonSerializable;
use ontio\common\ByteArray;
use ontio\crypto\KeyType;
use ontio\crypto\KeyParameters;

class ControlData implements JsonSerializable
{
  /** @var string */
  public $id;

  /** @var PrivateKey */
  public $encryptedKey;

  /** @var Address */
  public $address;

  /** @var string */
  public $salt;

  /** @var string */
  public $hash = 'sha256';

  /** @var string */
  public $publicKey;

  public function __construct(string $id, PrivateKey $encryptedKey, Address $address, string $salt)
  {
    $this->id = $id;
    $this->encryptedKey = $encryptedKey;
    $this->address = $address;
    $this->salt = $salt;
  }

  public function jsonSerialize()
  {
    $pk = $this->encryptedKey->jsonSerialize();
    $ret = [
      'id' => $this->id,
      'address' => $this->address->toBase58(),
      'salt' => $this->salt,
      'enc-alg' => 'aes-256-gcm',
      'hash' => $this->hash,
      'publicKey' => $this->publicKey
    ];
    return array_merge($pk, $ret);
  }

  public static function fromJsonObj($obj) : self
  {
    $key = ByteArray::fromBase64($obj->key);
    $keyType = KeyType::fromLabel($obj->algorithm);
    $params = KeyParameters::fromJsonObj($obj->parameters);
    $prikey = new PrivateKey($key, $keyType, $params);

    $ctrl = new ControlData($obj->id, $prikey, new Address($obj->address), $obj->salt);
    $ctrl->publicKey = $obj->publicKey;
    $ctrl->hash = $obj->hash;
    return $ctrl;
  }
}
