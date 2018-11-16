<?php

namespace ontio\core\payload;

use ontio\core\scripts\ScriptBuilder;
use ontio\common\ByteArray;
use ontio\core\scripts\ScriptReader;

class DeployCode implements Payload
{
  /** @var string */
  public $code;

  /** @var bool */
  public $needStorage;

  /** @var string */
  public $name;

  /** @var string */
  public $version;

  /** @var string */
  public $author;

  /** @var string */
  public $email;

  /** @var string */
  public $description;

  public function serialize() : string
  {
    $builder = new ScriptBuilder();
    $builder->pushVarBytes(ByteArray::fromHex($this->code));
    $builder->pushBool($this->needStorage);
    $builder->pushVarBytes(ByteArray::fromBinary($this->name));
    $builder->pushVarBytes(ByteArray::fromBinary($this->version));
    $builder->pushVarBytes(ByteArray::fromBinary($this->author));
    $builder->pushVarBytes(ByteArray::fromBinary($this->email));
    $builder->pushVarBytes(ByteArray::fromBinary($this->description));
    return $builder->toHex();
  }

  public function deserialize(ScriptReader $r)
  {
    $this->code = $r->readVarBytes()->toHex();
    $this->needStorage = $r->readBool();
    $this->name = $r->readVarBytes()->toHex();
    $this->version = $r->readVarBytes()->toHex();
    $this->author = $r->readVarBytes()->toHex();
    $this->email = $r->readVarBytes()->toHex();
    $this->description = $r->readVarBytes()->toHex();
  }
}
