<?php

namespace ontio\smartcontract\abi;

use \JsonSerializable;

class AbiFunction
{
  /** @var string */
  public $name;

  /** @var string */
  public $returnType;

  /** @var Parameter[] */
  public $parameters = [];

  /**
   * @param string $name
   * @param string $returnType
   * @param Parameter[] $parameters
   */
  public function __construct(string $name, string $returnType, $parameters = [])
  {
    $this->name = $name;
    $this->returnType = $returnType;
    $this->parameters = $parameters;
  }

  /**
   * @param string $name
   * @return Parameter|null
   */
  public function getParameter(string $name)
  {
    foreach ($this->parameters as $p) {
      if ($p->getName() === $name) return $p;
    }
    return null;
  }

  public function setParamsValue(Parameter ...$ps)
  {
    $args = func_get_args();
    foreach ($args as $arg) {
      $p = $this->getParameter($arg->name);
      $p->setValue($arg);
    }
  }

  public static function fromJsonObj($obj) : self
  {
    $f = new self();
    $f->name = $obj->name;
    $f->returnType = $obj->returnType;
    $f->parameters = array_map(function ($p) {
      return Parameter::fromJsonObj($p);
    }, $obj->parameters);
  }

  public function jsonSerialize()
  {
    return [
      'name' => $this->name,
      'returntype' => $this->returnType,
      'parameters' => $this->parameters
    ];
  }

  public function __toString()
  {
    return json_decode($this);
  }
}
