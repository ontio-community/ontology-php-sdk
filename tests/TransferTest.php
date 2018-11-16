<?php

use PHPUnit\Framework\TestCase;

use ontio\crypto\Address;
use ontio\core\transaction\TransactionBuilder;
use ontio\smartcontract\nativevm\OntAssetTxBuilder;
use ontio\common\ByteArray;
use ontio\core\scripts\ScriptReader;
use ontio\sdk\Wallet;
use ontio\crypto\PrivateKey;
use ontio\network\JsonRpc;
use ontio\crypto\ScryptParams;
use ontio\crypto\KeyType;
use ontio\crypto\KeyParameters;
use ontio\crypto\CurveLabel;
use ontio\crypto\SignatureScheme;

final class TransferTest extends TestCase
{
  /** @var Wallet */
  public static $wallet;

  /** @var PrivateKey */
  public static $adminPrivateKey;

  /** @var Address */
  public static $adminAddress;

  /** @var string */
  public static $gasLimit;

  /** @var string */
  public static $gasPrice;

  /** @var JsonRpc */
  public static $rpc;

  public static $sm2Account;

  public static function setUpBeforeClass()
  {
    // pwd: 123456
    $walletData = '{"name":"MyWallet","version":"1.1","scrypt":{"p":8,"n":16384,"r":8,"dkLen":64},"accounts":[{"address":"AYYpiV2q6bojSRHpxWHQfajVYhYUeeL7R4","enc-alg":"aes-256-gcm","key":"sESoKtN5UgdOAim5OgO4K5DNtHg1bQqToENWUCXFt7iFqaH+VAmzATEDBC8X2rjJ","algorithm":"SM2","salt":"xB4I3XP5weI6NHmueF9OEA==","parameters":{"curve":"sm2p256v1"},"label":"","publicKey":"131403dcadd0b157699c21dd2273d090449751c47fe43cdb06ce58f781e12932e8ba91","signatureScheme":"SM3withSM2","isDefault":true,"lock":false}]}';
    self::$wallet = Wallet::fromJson($walletData);
    self::$adminPrivateKey = self::$wallet->accounts[0]->exportPrivateKey('123456', self::$wallet->scrypt);
    self::$adminAddress = self::$wallet->accounts[0]->address;
    self::$gasLimit = '20000';
    self::$gasPrice = '0';

    self::$rpc = new JsonRpc('http://127.0.0.1:20336');
  }

  public function test_transfer_ont()
  {
    $prikey = new PrivateKey(ByteArray::fromHex('6f5a6887c8130f6e8c4bfaf8506c595168bf6ba9464dd5911ede2a56b63183a4'));
    $from = Address::fromPubKey($prikey->getPublicKey());
    $to = new Address('AH9B261xeBXdKH4jPyafcHcLkS2EKETbUj');

    $ontBuilder = new OntAssetTxBuilder();
    $tx = $ontBuilder->makeTransferTx('ONT', $from, $to, 170, self::$gasPrice, self::$gasLimit);
    $tx->nonce = '1546bef9';
    $txBuilder = new TransactionBuilder();
    $txBuilder->signTransaction($tx, $prikey);

    $res = self::$rpc->sendRawTransaction($tx->serialize(), false);
    $this->assertEquals('SUCCESS', $res->desc);
  }

  public function test_transfer_ong()
  {
    $prikey = new PrivateKey(ByteArray::fromHex('6f5a6887c8130f6e8c4bfaf8506c595168bf6ba9464dd5911ede2a56b63183a4'));
    $from = Address::fromPubKey($prikey->getPublicKey());
    $to = new Address('AH9B261xeBXdKH4jPyafcHcLkS2EKETbUj');

    $ongBuilder = new OntAssetTxBuilder();
    $tx = $ongBuilder->makeTransferTx('ONG', $from, $to, 170, self::$gasPrice, self::$gasLimit);

    $txBuilder = new TransactionBuilder();
    $txBuilder->signTransaction($tx, $prikey);

    $res = self::$rpc->sendRawTransaction($tx->serialize(), false);
    $this->assertEquals('SUCCESS', $res->desc);
  }

  public function test_get_balance()
  {
    $to = new Address('AH9B261xeBXdKH4jPyafcHcLkS2EKETbUj');
    $res = self::$rpc->getBalance($to);
    $this->assertEquals('SUCCESS', $res->desc);
  }

  public function test_transfer_with_sm2_account()
  {
    $ontBuilder = new OntAssetTxBuilder();
    $tx = $ontBuilder->makeTransferTx(
      'ONT',
      new Address("AYYpiV2q6bojSRHpxWHQfajVYhYUeeL7R4"),
      new Address("AcprovRtJETffQTFZKEdUrc1tEJebtrPyP"),
      100,
      self::$gasPrice,
      self::$gasLimit
    );

    $txBuilder = new TransactionBuilder();
    $txBuilder->signTransaction($tx, self::$adminPrivateKey, SignatureScheme::$Sm2WithSm3);

    $res = self::$rpc->sendRawTransaction($tx->serialize(), false);
    $this->assertEquals('SUCCESS', $res->desc);
  }
}
