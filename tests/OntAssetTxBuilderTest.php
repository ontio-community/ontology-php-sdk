<?php

use PHPUnit\Framework\TestCase;
use ontio\crypto\PrivateKey;
use ontio\common\ByteArray;
use ontio\crypto\Address;
use ontio\smartcontract\nativevm\OntAssetTxBuilder;
use ontio\core\transaction\TransactionBuilder;

final class OntAssetTxBuilderTest extends TestCase
{
  public function test_ts_result()
  {
    $prikey = new PrivateKey(ByteArray::fromHex('04b3f3c50133e59a0fdce3daed7aca023e9c2baaab4001375a299806bd7bbe3f'));
    $pubkey = $prikey->getPublicKey();

    $to = new Address('AH9B261xeBXdKH4jPyafcHcLkS2EKETbUj');
    $ontBuilder = new OntAssetTxBuilder();
    $tx = $ontBuilder->makeTransferTx('ONT', Address::fromPubKey($pubkey), $to, 170, 0, '20000');
    $tx->nonce = '1546bef9';

    $txBuilder = new TransactionBuilder();
    $txBuilder->signTransaction($tx, $prikey);

    $this->assertEquals(
      '00d11546bef90000000000000000204e0000000000007515908f2ecd8836df04135a56a3abbf4874f3227300c66b147515908f2ecd8836df04135a56a3abbf4874f3226a7cc8140eff5cd245a6e2c991579b1c2704fad116c395076a7cc802aa006a7cc86c51c1087472616e736665721400000000000000000000000000000000000000010068164f6e746f6c6f67792e4e61746976652e496e766f6b650001424101d1ecb664648754598c3b9138408ed192735b41ee5cb475be1fe517f307dced403b33a1c48d5b7556d2acc136f906b970a64072ffc99eb5705f5d4cdeb20f0eb323210344ea636caaebf23c7cec2219a75bd6260f891413467922975447ba57f3c824c6ac',
      $tx->serialize()
    );
  }
}
