<?php

namespace ontio\smartcontract\abi;

use ontio\core\scripts\ScriptBuilder;
use ontio\common\ByteArray;
use ontio\crypto\Address;
use \GMP;
use ontio\core\scripts\Opcode;

class NativeVmParamsBuilder extends ScriptBuilder
{
  public function pushParams(Parameter $params) : self
  {
    foreach ($params as $p) {
      /** @var Parameter $p */
      $type = $p->getType();
      $v = $p->getValue();
      switch ($type) {
        case ParameterType::ByteArray:
          return $this->pushVarBytes($v);
        case ParameterType::Int:
          return $this->pushInt($v, 4, true);
        case ParameterType::String:
          return $this->pushVarStr($v);
        case ParameterType::Address:
          return $this->pushAddress($v);
        default:
          throw new \InvalidArgumentException('Unsupported Parameter type: ' . $type);
      }
    }
  }

  public function pushCodeParamScript($obj) : self
  {
    if (is_string($obj)) {
      $this->pushHexString($obj);
    } else if (is_bool($obj)) {
      $this->pushBool($obj);
    } else if (is_int($obj)) {
      $this->pushNum($obj);
    } else if ($obj instanceof GMP) {
      $this->pushBigNum($obj);
    } else if ($obj instanceof Address) {
      $this->pushAddress($obj);
    } else if ($obj instanceof Struct) {
      foreach ($obj->list as $item) {
        $this->pushCodeParamScript($item);
        $this->pushInt(Opcode::DUPFROMALTSTACK);
        $this->pushInt(Opcode::SWAP);
        $this->pushInt(Opcode::APPEND);
      }
    }
    return $this;
  }

  public function pushNativeCodeScript($list)
  {
    foreach ($list as $v) {
      if (is_string($v)) {
        $this->pushHexString($v);
      } else if (is_bool($v)) {
        $this->pushBool($v);
      } else if (is_int($v)) {
        $this->pushNum($v);
      } else if ($v instanceof GMP) {
        $this->pushBigNum($v);
      } else if ($v instanceof Address) {
        $this->pushAddress($v);
      } else if ($v instanceof Struct) {
        $this->pushNum(0);
        $this->pushInt(Opcode::NEWSTRUCT);
        $this->pushInt(Opcode::TOALTSTACK);
        foreach ($v->list as $item) {
          $this->pushCodeParamScript($item);
          $this->pushInt(Opcode::DUPFROMALTSTACK);
          $this->pushInt(Opcode::SWAP);
          $this->pushInt(Opcode::APPEND);
        }
        $this->pushInt(Opcode::FROMALTSTACK);
      } else if (is_array($v) && self::isTypedArray($v, Struct::class)) {
        $this->pushNum(0);
        $this->pushInt(Opcode::NEWSTRUCT);
        $this->pushInt(Opcode::TOALTSTACK);
        foreach ($v as $vv) {
          $this->pushCodeParamScript($vv);
        }
        $this->pushInt(Opcode::FROMALTSTACK);
        $this->pushNum(count($v));
        $this->pushInt(Opcode::PACK);
      } else if (is_array($v)) {
        $this->pushNativeCodeScript($v);
        $this->pushNum(count($v));
        $this->pushInt(Opcode::PACK);
      }
    }
    return $this;
  }

  public static function isTypedArray($arr, $type) : bool
  {
    foreach ($arr as $item) {
      if (!($item instanceof $type)) return false;
    }
    return true;
  }
}
