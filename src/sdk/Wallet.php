<?php

namespace ontio\sdk;

use ontio\crypto\ScryptParams;
use ontio\sdk\Account;
use ontio\common\Util;
use \JsonSerializable;

class Wallet implements JsonSerializable
{
  /** @var string */
  public $name;
  /** @var string */
  public $defaultOntid;
  /** @var string */
  public $defaultAccountAddress;
  /** @var string */
  public $createTime;
  /** @var string */
  public $version;
  /** @var ScryptParams */
  public $scrypt;
  /** @var Identity[] */
  public $identities = [];
  /** @var Account[] */
  public $accounts = [];
  /** @var mixed */
  public $extra;

  public function addAccount(Account $acc)
  {
    foreach ($this->accounts as $a) {
      if ($a->address->toBase58() === $acc->address->toBase58()) return;
    }
    $this->accounts[] = $acc;
  }

  public function deleteAccount(Account $acc)
  {
    $as = [];
    foreach ($this->accounts as $a) {
      if ($a->address->toBase58() === $acc->address->toBase58()) continue;
      $as[] = $a;
    }
    $this->accounts = $as;
  }

  public function addIdentify(Identity $id)
  {
    foreach ($this->identities as $i) {
      if ($i->ontid === $id->ontid) return;
    }
    $this->identities[] = $id;
  }

  public function setDefaultAccount(string $address)
  {
    $this->defaultAccountAddress = $address;
  }

  public function setDefaultIdentity(string $ontid)
  {
    $this->defaultOntid = $ontid;
  }

  public static function create(string $name) : self
  {
    $w = new self();
    $w->name = $name;
    $w->create = (new \DateTime())->format(Util::JS_ISO);
    $w->version = '1.0';
    $w->scrypt = new ScryptParams();
    $w->scrypt->n = Constant::$DEFAULT_SCRYPT->cost;
    $w->scrypt->r = Constant::$DEFAULT_SCRYPT->blockSize;
    $w->scrypt->p = Constant::$DEFAULT_SCRYPT->parallel;
    $w->scrypt->dkLen = Constant::$DEFAULT_SCRYPT->size;
    return $w;
  }

  public static function fromJson(string $json) : self
  {
    return self::fromJsonObj(json_decode($json));
  }

  public static function fromJsonObj($obj) : self
  {
    $w = new self();
    $w->name = $obj->name;
    $w->defaultOntid = $obj->defaultOntid;
    $w->defaultAccountAddress = $obj->defaultAccountAddress;
    $w->createTime = $obj->createTime;
    $w->version = $obj->version;
    $w->scrypt = ScryptParams::fromJsonObj($obj->scrypt);
    if ($obj->identities) {
      $w->identities = array_map(function ($i) {
        return Identity::fromJsonObj($i);
      }, $obj->identities);
    }
    if ($obj->accounts) {
      $w->accounts = array_map(function ($a) {
        return Account::fromJsonObj($a);
      }, $obj->accounts);
    }
    $w->extra = $obj->extra;
    return $w;
  }

  public function jsonSerialize()
  {
    $identities = array_map(function ($id) {
      return $id->jsonSerialize();
    }, $this->identities);

    $accounts = array_map(function ($a) {
      return $a->jsonSerialize();
    }, $this->accounts);

    return [
      'name' => $this->name,
      'defaultOntid' => $this->defaultOntid,
      'defaultAccountAddress' => $this->defaultAccountAddress,
      'createTime' => $this->createTime,
      'version' => $this->version,
      'scrypt' => $this->scrypt,
      'identities' => $identities,
      'accounts' => $accounts,
      'extra' => null
    ];
  }
}
