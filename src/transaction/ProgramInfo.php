<?php

namespace ontio\transaction;

use ontio\crypto\PublicKey;

class ProgramInfo
{
  /** @var int */
  public $M;

  /** @var PublicKey[] */
  public $pubKeys;
}
