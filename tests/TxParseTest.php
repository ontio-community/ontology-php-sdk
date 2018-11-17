<?php

use PHPUnit\Framework\TestCase;

use ontio\crypto\Address;
use ontio\core\transaction\TransactionBuilder;
use ontio\smartcontract\nativevm\OntAssetTxBuilder;
use ontio\common\ByteArray;
use ontio\core\scripts\ScriptReader;
use ontio\crypto\PrivateKey;


final class TxParseTest extends TestCase
{
  /** @var Address */
  public static $from;

  /** @var Address */
  public static $to;

  public static function setUpBeforeClass()
  {
    self::$from = new Address('AJAhnApxyMTBTHhfpizua48EEFUxGg558x');
    self::$to = new Address('ALFZykMAYibLoj66jcBdbpTnrBCyczf4CL');
  }

  public function test_transfer_15_ont()
  {
    $builder = new OntAssetTxBuilder();
    $tx = $builder->makeTransferTx('ONT', self::$from, self::$to, 15, '500', '20000', $from);
    $transfer = $builder->deserializeTransferTx(new ScriptReader(ByteArray::fromHex($tx->serialize())));
    $this->assertEquals(15, $transfer->amount);
    $this->assertEquals('AJAhnApxyMTBTHhfpizua48EEFUxGg558x', $transfer->from->toBase58());
    $this->assertEquals('ALFZykMAYibLoj66jcBdbpTnrBCyczf4CL', $transfer->to->toBase58());
    $this->assertEquals('ONT', $transfer->tokenType);
  }

  public function test_transfer_1000_ont()
  {
    $builder = new OntAssetTxBuilder();
    $tx = $builder->makeTransferTx('ONT', self::$from, self::$to, 10000, '500', '20000', $from);
    $transfer = $builder->deserializeTransferTx(new ScriptReader(ByteArray::fromHex($tx->serialize())));
    $this->assertEquals(10000, $transfer->amount);
    $this->assertEquals('AJAhnApxyMTBTHhfpizua48EEFUxGg558x', $transfer->from->toBase58());
    $this->assertEquals('ALFZykMAYibLoj66jcBdbpTnrBCyczf4CL', $transfer->to->toBase58());
    $this->assertEquals('ONT', $transfer->tokenType);
  }

  public function test_transfer_0_33_ong()
  {
    $builder = new OntAssetTxBuilder();
    $tx = $builder->makeTransferTx('ONG', self::$from, self::$to, 0.33 * 1e9, '500', '20000', $from);
    $transfer = $builder->deserializeTransferTx(new ScriptReader(ByteArray::fromHex($tx->serialize())));
    $this->assertEquals(0.33 * 1e9, $transfer->amount);
    $this->assertEquals('AJAhnApxyMTBTHhfpizua48EEFUxGg558x', $transfer->from->toBase58());
    $this->assertEquals('ALFZykMAYibLoj66jcBdbpTnrBCyczf4CL', $transfer->to->toBase58());
    $this->assertEquals('ONG', $transfer->tokenType);
  }

  public function test_transfer_123_ong()
  {
    $builder = new OntAssetTxBuilder();
    $tx = $builder->makeTransferTx('ONG', self::$from, self::$to, 123 * 1e9, '500', '20000', $from);
    $transfer = $builder->deserializeTransferTx(new ScriptReader(ByteArray::fromHex($tx->serialize())));
    $this->assertEquals(123 * 1e9, $transfer->amount);
    $this->assertEquals('AJAhnApxyMTBTHhfpizua48EEFUxGg558x', $transfer->from->toBase58());
    $this->assertEquals('ALFZykMAYibLoj66jcBdbpTnrBCyczf4CL', $transfer->to->toBase58());
    $this->assertEquals('ONG', $transfer->tokenType);
  }

  public function test_transfer_1_533_ong()
  {
    $builder = new OntAssetTxBuilder();
    $tx = $builder->makeTransferTx('ONG', self::$from, self::$to, 1.533 * 1e9, '500', '20000', $from);
    $transfer = $builder->deserializeTransferTx(new ScriptReader(ByteArray::fromHex($tx->serialize())));
    $this->assertEquals(1.533 * 1e9, $transfer->amount);
    $this->assertEquals('AJAhnApxyMTBTHhfpizua48EEFUxGg558x', $transfer->from->toBase58());
    $this->assertEquals('ALFZykMAYibLoj66jcBdbpTnrBCyczf4CL', $transfer->to->toBase58());
    $this->assertEquals('ONG', $transfer->tokenType);
  }

  public function test_sign()
  {
    $priKey = new PrivateKey(ByteArray::fromHex('6d222ae7c875f0ffda7697b1979a5bf5438f2a80e27198740b1330aec2bf0bb7'));
    $pubkey = $priKey->getPublicKey();

    $from = Address::fromPubKey($pubkey);
    $to = new Address('AH9B261xeBXdKH4jPyafcHcLkS2EKETbUj');
    $ontBuilder = new OntAssetTxBuilder();
    $tx = $ontBuilder->makeTransferTx('ONT', $from, $to, 17, '500', '20000');
    $tx->nonce = 'f37fe625';

    $txBuilder = new TransactionBuilder();
    $txBuilder->signTransaction($tx, $priKey);

    $this->assertEquals(
      '0122c0f110f1d00d1c04cede664142ffbc1d1a274d77122504115cdb705e1037ba1438115b65b469febbc0cbc25d689f30912ba56b85f9e5d873369ad9ac9ec164',
      $tx->sigs[0]->sigData[0]
    );
  }
}
