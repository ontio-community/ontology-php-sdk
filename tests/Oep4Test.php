<?php

use PHPUnit\Framework\TestCase;
use ontio\crypto\Address;
use ontio\sdk\Wallet;
use ontio\crypto\PrivateKey;
use ontio\network\JsonRpc;
use ontio\core\transaction\TransactionBuilder;
use ontio\network\WebsocketRpc;
use ontio\smartcontract\neovm\Oep4TxBuilder;
use ontio\common\Endian;
use ontio\common\BigInt;
use ontio\smartcontract\neovm\OepState;
use ontio\common\ByteArray;

const codeHash = 'cf8a3226f873bb73ed66039de4ff6a32b00693ac';

final class OntidContractTxBuilderTest extends TestCase
{
  /** @var Wallet */
  public static $wallet;

  /** @var PrivateKey */
  public static $prikey;

  /** @var Address */
  public static $addr;

  /** @var string */
  public static $gasLimit;

  /** @var string */
  public static $gasPrice;

  /** @var JsonRpc */
  public static $rpc;

  /** @var WebsocketRpc */
  public static $wsRpc;

  public static function setUpBeforeClass()
  {
    // pwd: 123456
    $walletData = '{"name":"MyWallet","version":"1.1","scrypt":{"p":8,"n":16384,"r":8,"dkLen":64},"accounts":[{"address":"ASSxYHNSsh4FdF2iNvHdh3Np2sgWU21hfp","enc-alg":"aes-256-gcm","key":"t2Kk2jNL4BAoXlYn309DKfxogxJRNvsJ8+GG4kiMB+UvWGXEilYRzfIYeNZbfVbu","algorithm":"ECDSA","salt":"CdRa1hTiOaVESNfJJmcMNw==","parameters":{"curve":"P-256"},"label":"","publicKey":"0344ea636caaebf23c7cec2219a75bd6260f891413467922975447ba57f3c824c6","signatureScheme":"SHA256withECDSA","isDefault":true,"lock":false},{"address":"AL9PtS6F8nue5MwxhzXCKaTpRb3yhtsix5","enc-alg":"aes-256-gcm","key":"vwIgX3qJO+1XikdPAfjAu/clsgS2l2xkEWsRR9XZQ8OyFViX+r/6Yq+cV0wnKQUM","algorithm":"SM2","salt":"xzvrFkHAgsEeX64V+4mpLw==","parameters":{"curve":"sm2p256v1"},"label":"","publicKey":"131403a9b89a0443ded240c3dee97221353d000d0dc905b7c085f4ef558b234a75e122","signatureScheme":"SM3withSM2","isDefault":false,"lock":false}]}';
    self::$wallet = Wallet::fromJson($walletData);
    self::$prikey = self::$wallet->accounts[0]->exportPrivateKey('123456', self::$wallet->scrypt);
    self::$addr = self::$wallet->accounts[0]->address;
    self::$gasLimit = '20000';
    self::$gasPrice = '0';

    self::$rpc = new JsonRpc('http://127.0.0.1:20336');
    self::$wsRpc = new WebsocketRpc('ws://127.0.0.1:20335');

    self::deployTestContract();
  }

  public static function deployTestContract()
  {
    $code = trim(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'Oep4Test.avm'));

    $txBuilder = new TransactionBuilder();
    $tx = $txBuilder->makeDeployCodeTransaction(
      $code,
      'name',
      '1.0',
      'alice',
      'mail',
      'desc',
      true,
      self::$gasPrice,
      '30000000',
      self::$addr
    );

    $txBuilder->signTransaction($tx, self::$prikey);
    $res = self::$wsRpc->sendRawTransaction($tx->serialize(), false, true);
  }

  public function test_init()
  {
    $builder = new Oep4TxBuilder(new Address(codeHash));
    $tx = $builder->makeInitTx(self::$gasPrice, self::$gasLimit, self::$addr);

    $txBuilder = new TransactionBuilder();
    $txBuilder->signTransaction($tx, self::$prikey);

    $res = self::$wsRpc->sendRawTransaction($tx->serialize(), false, true);
    $this->assertEquals('SUCCESS', $res->Desc);
  }

  public function test_query_name()
  {
    $builder = new Oep4TxBuilder(new Address(codeHash));
    $tx = $builder->makeQueryNameTx();
    $res = self::$rpc->sendRawTransaction($tx->serialize(), true);
    $this->assertEquals('SUCCESS', $res->desc);
  }

  public function test_query_symbol()
  {
    $builder = new Oep4TxBuilder(new Address(codeHash));
    $tx = $builder->makeQuerySymbolTx();
    $res = self::$rpc->sendRawTransaction($tx->serialize(), true);
    $this->assertEquals('SUCCESS', $res->desc);
  }

  public function test_query_decimals()
  {
    $builder = new Oep4TxBuilder(new Address(codeHash));
    $tx = $builder->makeQueryDecimalsTx();
    $res = self::$rpc->sendRawTransaction($tx->serialize(), true);
    $this->assertEquals('SUCCESS', $res->desc);
  }

  public function test_query_total_supply()
  {
    $builder = new Oep4TxBuilder(new Address(codeHash));
    $tx = $builder->makeQueryTotalSupplyTx();
    $res = self::$rpc->sendRawTransaction($tx->serialize(), true);
    $this->assertEquals('SUCCESS', $res->desc);
  }

  public function test_query_balance()
  {
    $builder = new Oep4TxBuilder(new Address(codeHash));
    $tx = $builder->makeQueryBalanceOfTx(self::$addr);
    $res = self::$rpc->sendRawTransaction($tx->serialize(), true);
    $this->assertEquals('SUCCESS', $res->desc);
  }

  public function test_transfer()
  {
    $builder = new Oep4TxBuilder(new Address(codeHash));
    $from = self::$addr;

    $toPrikey = self::$wallet->accounts[1]->exportPrivateKey('123456', self::$wallet->scrypt);
    $to = self::$wallet->accounts[1]->address;

    $tx = $builder->makeTransferTx(self::$addr, $to, '10000', self::$gasPrice, self::$gasLimit, $from);

    $txBuilder = new TransactionBuilder();
    $txBuilder->signTransaction($tx, self::$prikey);

    $res = self::$wsRpc->sendRawTransaction($tx->serialize(), false, true);
    $this->assertEquals('SUCCESS', $res->Desc);
  }

  public function test_approve()
  {
    $builder = new Oep4TxBuilder(new Address(codeHash));
    $owner = self::$addr;

    $spenderPrikey = self::$wallet->accounts[1]->exportPrivateKey('123456', self::$wallet->scrypt);
    $spender = self::$wallet->accounts[1]->address;

    $tx = $builder->makeApproveTx($owner, $spender, '10000', self::$gasPrice, self::$gasLimit, self::$addr);

    $txBuilder = new TransactionBuilder();
    $txBuilder->signTransaction($tx, self::$prikey);

    $res = self::$wsRpc->sendRawTransaction($tx->serialize(), false, true);
    $this->assertEquals('SUCCESS', $res->Desc);
  }

  public function test_query_allowance()
  {
    $builder = new Oep4TxBuilder(new Address(codeHash));
    $owner = self::$addr;

    $spenderPrikey = self::$wallet->accounts[1]->exportPrivateKey('123456', self::$wallet->scrypt);
    $spender = self::$wallet->accounts[1]->address;

    $tx = $builder->makeQueryAllowanceTx($owner, $spender);

    $res = self::$wsRpc->sendRawTransaction($tx->serialize(), true);
    $this->assertEquals('SUCCESS', $res->Desc);

    $this->assertTrue(BigInt::fromHex($res->Result->Result)->equals(10000));
  }

  public function test_transfer_from()
  {
    $builder = new Oep4TxBuilder(new Address(codeHash));
    $owner = self::$addr;

    $spenderPrikey = self::$wallet->accounts[1]->exportPrivateKey('123456', self::$wallet->scrypt);
    $spender = self::$wallet->accounts[1]->address;

    $tx = $builder->makeTransferFromTx($spender, $spender, $owner, '10000', self::$gasPrice, self::$gasLimit, self::$addr);

    $txBuilder = new TransactionBuilder();
    $txBuilder->signTransaction($tx, self::$prikey);

    $res = self::$wsRpc->sendRawTransaction($tx->serialize(), true);
    $this->assertEquals('SUCCESS', $res->Desc);
  }

  public function test_transfer_multi()
  {
    $addr1 = self::$addr;

    $prikey2 = self::$wallet->accounts[1]->exportPrivateKey('123456', self::$wallet->scrypt);
    $addr2 = self::$wallet->accounts[1]->address;

    $prikey3 = PrivateKey::random();
    $addr3 = Address::fromPubKey($prikey3->getPublicKey());

    $state1 = new OepState($addr1, $addr2, '200');
    $state2 = new OepState($addr1, $addr3, '300');

    $builder = new Oep4TxBuilder(new Address(codeHash));
    $tx = $builder->makeTransferMultiTx([$state1, $state2], self::$gasPrice, self::$gasLimit, self::$addr);

    $txBuilder = new TransactionBuilder();
    $txBuilder->signTransaction($tx, self::$prikey);
    $txBuilder->addSig($tx, self::$prikey);

    $res = self::$wsRpc->sendRawTransaction($tx->serialize(), true);
    $this->assertEquals('SUCCESS', $res->Desc);
  }
}
