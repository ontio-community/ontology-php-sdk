<?php

namespace ontio\crypto;

use ontio\Constant;


class ScryptParams
{
  /**
   * The CPU difficultly (must be a power of 2, greater than 1)
   *
   * @var int
   */
  public $n = 16384;

  /**
   * The memory difficulty
   *
   * @var int
   */
  public $r = 8;

  /**
   * The parallel difficulty
   *
   * @var int
   */
  public $p = 8;

  /**
   * The length of hash
   *
   * @var int
   */
  public $dkLen = 64;

  public static function fromJsonObj($obj) : self
  {
    $sp = new self();
    $sp->n = $obj->n;
    $sp->r = $obj->r;
    $sp->p = $obj->p;
    $sp->dkLen = $obj->dkLen;
    return $sp;
  }

  /** @var ScryptParams */
  public static $default;

  public static function init()
  {
    self::$default = new self();
    self::$default->n = Constant::$DEFAULT_SCRYPT->cost;
    self::$default->r = Constant::$DEFAULT_SCRYPT->blockSize;
    self::$default->p = Constant::$DEFAULT_SCRYPT->parallel;
    self::$default->dkLen = Constant::$DEFAULT_SCRYPT->size;
  }
}

ScryptParams::init();
