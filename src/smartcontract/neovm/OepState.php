<?php

namespace ontio\smartcontract\neovm;

use GMP;
use ontio\crypto\Address;

class OepState
{
  /** @var string */
  public $from;

  /** @var string */
  public $to;

  /** @var GMP */
  public $amount;

  public function __construct(Address $from, Address $to, string $amount)
  {
    $this->from = $from->serialize();
    $this->to = $to->serialize();
    $this->amount = gmp_init($amount);
  }
}
