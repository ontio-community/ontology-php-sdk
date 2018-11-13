<?php

namespace ontio\common;

class Fixed64
{
  /** @var int */
  public $value;

  public function __construct(int $value = 0)
  {
    $this->value = $value;
  }

  public static function deserialize(ForwardBuffer $buf) : self
  {
    return new self($buf->readUInt64LE());
  }

  public function serialize() : string
  {
    return ByteArray::fromInt($this->value, 8, true)->toHex();
  }
}
