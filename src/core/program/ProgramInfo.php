<?php

namespace ontio\core\program;

use ontio\crypto\PublicKey;

class ProgramInfo
{
  /** @var int */
  public $M;

  /** @var PublicKey[] */
  public $pubKeys;
}
