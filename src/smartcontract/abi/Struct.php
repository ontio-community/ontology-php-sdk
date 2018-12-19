<?php

namespace ontio\smartcontract\abi;

use ontio\core\scripts\ScriptReader;
use ontio\common\ByteArray;


class Struct
{
  public $list = [];

  public function add(...$args)
  {
    $args = func_get_args();
    foreach ($args as $arg) {
      $this->list[] = $arg;
    }
  }

  public static function fromHex(string $hex) {
    $r = new ScriptReader(ByteArray::fromHex($hex));
    return $r->readStruct();
  }
}
