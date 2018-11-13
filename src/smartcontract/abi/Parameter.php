<?php

namespace ontio\smartcontract\abi;

use ontio\common\ByteArray;

class Parameter
{
  /** @var string */
  public $name;

  /**
   * Value of ParameterType
   *
   * @var string
   */
  public $type;

  /** @var string|int|ByteArray */
  public $value;

  public function __construct(string $name, int $type, $value)
  {
    $this->name = $name;
    $this->type = $type;
    $this->value = $value;
  }

  public function getName() : string
  {
    return $this->name;
  }

  public function getType() : int
  {
    return $this->type;
  }

  /**
   * @return any
   */
  public function getValue()
  {
    return $this->value;
  }

  public function setValue($value) : bool
  {
    if ($value->type === $this->type && $value->name === $this->name && $value->value !== null) {
      $this->value = $value;
      return true;
    }
    return false;
  }

  public function fromJsonObj($obj) : self
  {
    $p = new self();
    $p->name = $obj->name;
    $p->type = $obj->type;
    $p->value = $obj->value;
    return $p;
  }
}
