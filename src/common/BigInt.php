<?php

namespace ontio\common;

use \GMP;

const SIZE = 8;

class BigInt
{
  /** @var GMP */
  public $value;

  /**
   * @param int|string|GMP $value
   */
  public function __construct($value)
  {
    if (is_numeric($value)) {
      $this->value = gmp_init($value);
    } else if ($value instanceof GMP) {
      $this->value = $value;
    } else {
      throw new \InvalidArgumentException('deformed value, must be numeric');
    }
  }

  public static function fromHex(string $hex) : self
  {
    $v = gmp_import(hex2bin($hex), 1, GMP_LSW_FIRST);
    return new self($v);
  }

  public function toHex() : string
  {
    return bin2hex(gmp_export($this->value, 2, GMP_LSW_FIRST));
  }
}
