<?php

namespace ontio\transaction;

use ontio\common\ForwardBuffer;
use ontio\common\ByteArray;
use ontio\crypto\PublicKey;

class ScriptReader extends ForwardBuffer
{
  public function readOpcode() : int
  {
    return $this->readUInt8();
  }

  public function readBool() : bool
  {
    $op = $this->readOpcode();
    return $op === Opcode::PUSHT ? true : false;
  }

  public function readBytes() : ByteArray
  {
    $code = $this->readOpcode();
    $len;
    if ($code === Opcode::PUSHDATA4) {
      $len = $this->readUInt32LE();
    } else if ($code === Opcode::PUSHDATA2) {
      $len = $this->readUInt16LE();
    } else if ($code === Opcode::PUSHDATA1) {
      $len = $this->readUInt8();
    } else if ($code <= Opcode::PUSHBYTES75 && $code >= Opcode::PUSHBYTES1) {
      $len = $code - Opcode::PUSHBYTES1 + 1;
    } else {
      throw new \InvalidArgumentException('Unexpected opcode: ' . $code);
    }
    return $this->forward($len);
  }

  public function readVarInt() : int
  {
    $len = $this->readUInt8();
    if ($len === 0xfd) {
      return $this->readUInt16LE();
    } else if ($len === 0xfe) {
      return $this->readUInt32LE();
    } else if ($len === 0xff) {
      return $this->readUInt64LE();
    }
    return $len;
  }

  public function readVarBytes() : ByteArray
  {
    $len = $this->readVarInt();
    return $this->forward($len);
  }

  /**
   * @return int|GMP
   */
  public function readNum()
  {
    $op = $this->readOpcode();
    $num = $op - Opcode::PUSH1 + 1;
    if ($code === Opcode::PUSH0) {
      return 0;
    } else if (1 <= $num && $num <= 16) {
      return $num;
    }
    $bb = $this->readVarBytes();
    return gmp_init('0x' . $bb->toHex());
  }

  public function readPubKey() : PublicKey
  {
    $bytes = $this->readBytes();
    return PublicKey::fromHex(new ForwardBuffer($bytes));
  }
}
