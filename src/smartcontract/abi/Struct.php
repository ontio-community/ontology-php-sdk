<?php

namespace ontio\smartcontract\abi;

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
}
