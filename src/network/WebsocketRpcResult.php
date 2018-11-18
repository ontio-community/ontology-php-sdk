<?php

namespace ontio\network;

class WebsocketRpcResult
{
  /** @var string */
  public $Action;

  /** @var string */
  public $Desc;

  /** @var int */
  public $Error;

  /** @var string */
  public $Id;

  public $Result;

  /** @var string */
  public $Version;

  public static function fromJson($obj) : self
  {
    $r = new self();
    $r->Action = $obj->Action;
    $r->Id = $obj->Id;
    $r->Desc = $obj->Desc;
    $r->Error = $obj->Error;
    $r->Result = $obj->Result;
    $r->Version = $obj->Version;
    return $r;
  }
}
