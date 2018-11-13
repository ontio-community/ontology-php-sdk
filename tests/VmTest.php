<?php

use PHPUnit\Framework\TestCase;
use ontio\smartcontract\abi\Struct;
use ontio\smartcontract\abi\NativeVmParamsBuilder;
use ontio\crypto\PrivateKey;
use ontio\common\ByteArray;
use ontio\crypto\Address;

final class VmTest extends TestCase
{
  public function test_struct()
  {
    $priKey = new PrivateKey(ByteArray::fromHex('6d222ae7c875f0ffda7697b1979a5bf5438f2a80e27198740b1330aec2bf0bb7'));
    $pubkey = $priKey->getPublicKey();

    $from = Address::fromPubKey($pubkey);
    $to = new Address('AH9B261xeBXdKH4jPyafcHcLkS2EKETbUj');

    $struct = new Struct();
    $struct->add($from, $to, 17);
    $list = [[$struct]];

    $builder = new NativeVmParamsBuilder();
    $builder->pushNativeCodeScript($list);
    $params = $builder->toHex();

    $this->assertEquals(
      '00c66b144868a5ff16176fedab8df086fc36f7d31bab14226a7cc8140eff5cd245a6e2c991579b1c2704fad116c395076a7cc801116a7cc86c51c1',
      $params
    );
  }
}
