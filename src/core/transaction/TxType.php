<?php

namespace ontio\core\transaction;

class TxType
{
  const BookKeeper = 0x02;
  const Claim = 0x03;
  const Deploy = 0xd0;
  const Invoke = 0xd1;
  const Enrollment = 0x04;
  const Vote = 0x05;
}
