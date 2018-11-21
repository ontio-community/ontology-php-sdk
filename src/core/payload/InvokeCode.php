<?php

namespace ontio\core\payload;

use ontio\core\scripts\ScriptBuilder;
use ontio\common\ByteArray;
use ontio\core\scripts\ScriptReader;

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
