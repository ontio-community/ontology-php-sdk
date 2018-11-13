<?php

namespace ontio\network;

class JsonRpcResult
{
  /** @var string */
  public $desc;
  /** @var int */
  public $error;
  /** @var int */
  public $id;
  public $result;

  public static function fromJson($obj) : self
  {
    $r = new self();
    $r->desc = $obj->desc;
    $r->error = $obj->error;
    $r->id = $obj->id;
    $r->result = $obj->result;
    return $r;
  }
}
