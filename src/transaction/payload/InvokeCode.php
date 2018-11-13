<?php

namespace ontio\transaction\payload;

use ontio\transaction\ScriptBuilder;
use ontio\common\ByteArray;
use ontio\transaction\ScriptReader;

class InvokeCode extends Payload
{
  /**
   * Hex encoded string
   *
   * @var string
   */
  public $code;

  public function serialize() : string
  {
    $builder = new ScriptBuilder();
    return $builder->pushVarBytes(ByteArray::fromHex($this->code))->toHex();
  }

  public function deserialize(ScriptReader $r)
  {
    $this->code = $r->readVarBytes()->toHex();
  }
}
