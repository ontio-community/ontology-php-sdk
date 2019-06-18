<?php

namespace ontio\smartcontract\abi;

class AbiInfo
{
  /** @var string */
  public $hash;

  /** @var string */
  public $entrypoint;

  /** @var AbiFunction[] */
  public $functions = [];

  public static function fromJson(string $json) : self
  {
    $info = new self;
    $data = json_decode($json);
    
    if ($data->contractHash) {
      $info->hash = $data->contractHash;
    } else {
      $info->hash = $data->hash;
    }

    if ($data->abi) {
      $data = $data->abi;
    }

    $info->entrypoint = $data->entrypoint;

    $info->functions = array_map(function ($f) {
      return AbiFunction::fromJsonObj($f);
    }, $data->functions);

    return $info;
  }

  public function getHash() : string
  {
    return $this->hash;
  }

  public function getEntryPoint() : string
  {
    return $this->entrypoint;
  }

  public function getFunction(string $name) : AbiFunction
  {
    foreach ($this->functions as $f) {
      if ($f->name === $name) return $f;
    }
    throw new \InvalidArgumentException('not found');
  }
}
