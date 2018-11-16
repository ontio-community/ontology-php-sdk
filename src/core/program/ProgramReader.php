<?php

namespace ontio\core\program;

use \GMP;

class ProgramReader extends ScriptReader
{
  /**
   * @return string[]
   */
  public function readParams()
  {
    $sig = [];
    while (!$this->isEnd()) {
      $sig[] = $this->readBytes()->toHex();
    }
    return $sig;
  }

  /**
   * @return ProgramInfo
   */
  public function readInfo()
  {
    $info = new ProgramInfo();
    $end = $this->buf->slice(-2, 2)->readUInt16(0);
    if ($end === Opcode::CHECKSIG) {
      $info->M = 1;
      $info->pubKeys = [$this->readPubKey()];
      return $info;
    }

    if ($end === Opcode::CHECKMULTISIG) {
      $info->pubKeys = [];
      $m = $this->readNum();
      $n = (new ScriptReader($this->buf->slice(-4, 2)))->readNum();
      if ($n instanceof GMP) $n = gmp_intval($n);
      for ($i = 0; $i < $n; $i++) {
        $info->pubKeys[] = $this->readPubKey();
      }
      return $info;
    }

    throw new \InvalidArgumentException('Unsupported program');
  }
}
