<?php

namespace ontio\smartcontract\abi;

use ontio\smartcontract\abi\ParameterTypeVal;
use ontio\common\ByteArray;

class StructRawField
{
  /** @var ParameterTypeVal */
  public $type;

  /** @var ByteArray */
  public $bytes;

  public function __construct(int $type, ByteArray $bytes)
  {
    $this->type = $type;
    $this->bytes = $bytes;
  }
}
