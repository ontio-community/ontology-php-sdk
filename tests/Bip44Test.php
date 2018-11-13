<?php

use PHPUnit\Framework\TestCase;
use ontio\Account;

final class Bip44Test extends TestCase
{
  public function test_bip44()
  {
    $mnemonic = 'hill ready family useful detect bacon visit canoe recall circle topple claw sheriff universe robust lounge cluster duty vast excuse weasel grunt junk actor';
    $acc = Account::importFromMnemonic($mnemonic);
    $pk = $acc->exportPrivateKey();
    $this->assertEquals('AM57cppabEf4JeBXXGAPvRSLmYpqTmQ3sS', $acc->address->toBase58());
  }
}
