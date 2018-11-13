<?php

namespace ontio\transaction;

use ontio\common\ByteArray;
use \GMP;
use ontio\common\BigInt;
use ontio\smartcontract\abi\Struct;
use ontio\smartcontract\abi\ParameterTypeVal;
use ontio\ErrorCode;
use ontio\crypto\PublicKey;
use ontio\crypto\Address;
use ontio\crypto\KeyType;

class ScriptBuilder extends ByteArray
{
  public function pushBool(bool $b) : self
  {
    return $this->pushInt($b ? Opcode::PUSHT : Opcode::PUSHF);
  }

  public function pushNum(int $n) : self
  {
    if ($n === -1) {
      $this->pushInt(Opcode::PUSHM1);
    } else if ($n === 0) {
      $this->pushInt(Opcode::PUSH0);
    } else if ($n > 0 && $n < 16) {
      $this->pushInt(Opcode::PUSH1 - 1 + $n);
    } else {
      $bn = gmp_init($n);
      $this->pushBigNum($bn);
    }
    return $this;
  }

  public function pushBigNum(GMP $n) : self
  {
    if (gmp_cmp($n, -1) === 0) {
      $this->pushInt(Opcode::PUSHM1);
    } else if (gmp_cmp($n, 0) === 0) {
      $this->pushInt(Opcode::PUSH0);
    } else if (gmp_cmp($n, 0) > 0 && gmp_cmp($n, 16) < 0) {
      $this->pushInt(Opcode::PUSH1 - 1 + gmp_intval($n));
    } else {
      $this->pushHexString((new BigInt($n))->toHex());
    }
    return $this;
  }

  public function pushHexString(string $hex) : self
  {
    $len = strlen($hex) / 2;
    if ($len < Opcode::PUSHBYTES75) {
      $this->pushInt($len);
    } else if ($len < 0x100) {
      $this->pushInt(Opcode::PUSHDATA1);
      $this->pushInt($len);
    } else if ($len < 0x10000) {
      $this->pushInt(Opcode::PUSHDATA2);
      $this->pushInt($len, 2, true);
    } else {
      $this->pushInt(Opcode::PUSHDATA4);
      $this->pushInt($len, 4, true);
    }
    return $this->pushArray(ByteArray::fromHex($hex));
  }

  public function pushVarInt(int $num)
  {
    if ($num < 0xfd) {
      $this->pushInt($num);
    } else if ($num < 0xffff) {
      $this->pushInt(0xfd);
      $this->pushInt($num, 2, true);
    } else if ($num <= 0xffffffff) {
      $this->pushInt(0xfe);
      $this->pushInt($num, 4, true);
    } else {
      $this->pushInt(0xff);
      $this->pushInt($num, 8, true);
    }
    return $this;
  }

  public function pushStruct(Struct $s) : self
  {
    $this->pushInt(ParameterTypeVal::Struct);
    $this->pushInt(count($s->list));
    foreach ($s->list as $v) {
      if (is_string($v)) {
        $this->pushInt(ParameterTypeVal::ByteArray);
        $this->pushArray(ByteArray::fromHex($v));
      } else if (is_int($v)) {
        $this->pushInt(ParameterTypeVal::Integer);
        $this->pushVarInt($v);
      } else {
        throw new \InvalidArgumentException(ErrorCode::INVALID_PARAMS);
      }
    }
    return $this;
  }

  public function pushBytes(ByteArray $bytes) : self
  {
    $len = $bytes->length();
    if ($len === 0) {
      throw new \InvalidArgumentException('pushBytes error, bytes is empty.');
    }

    if ($len <= Opcode::PUSHBYTES75 + 1 - Opcode::PUSHBYTES1) {
      $this->push($len + Opcode::PUSHBYTES1 - 1);
    } else if ($len < 0x100) {
      $this->push(Opcode::PUSHDATA1)->push($len);
    } else if ($len < 0x10000) {
      $this->push(Opcode::PUSHDATA2)->pushArray(ByteArray::fromInt($len, 2, true));
    } else if ($len < 0x100000000) {
      $this->push(Opcode::PUSHDATA4)->pushArray(ByteArray::fromInt($len, 4, true));
    } else {
      throw new \InvalidArgumentException(ErrorCode::INVALID_PARAMS);
    }

    $this->pushArray($bytes);
    return $this;
  }

  public function pushPubKey(PublicKey $pk) : self
  {
    switch ($pk->algorithm->value) {
      case KeyType::$Ecdsa->value:
        $this->pushVarBytes($pk->key);
        break;
      case KeyType::$Eddsa->value:
      case KeyType::$Sm2->value:
        $buf = new ByteArray();
        $buf->pushInt($pk->algorithm->value);
        $buf->pushInt($pk->parameters->curve->value);
        $buf->pushArray($pk->key);
        $this->pushVarBytes($buf);
        break;
    }
    return $this;
  }

  public function pushOpcode(int $op) : self
  {
    $this->pushArray(ByteArray::fromInt($op));
    return $this;
  }

  public function pushVarBytes(ByteArray $bytes) : self
  {
    $this->pushVarInt($bytes->length());
    $this->pushArray($bytes);
    return $this;
  }

  public function pushVarStr(string $str) : self
  {
    $raw = ByteArray::fromBinary($str);
    $this->pushVarInt($raw->length());
    $this->pushArray($raw);
    return $this;
  }

  public function pushAddress(Address $addr) : self
  {
    $this->pushHexString($addr->serialize());
    return $this;
  }
}
