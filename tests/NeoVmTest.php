<?php

use PHPUnit\Framework\TestCase;
use ontio\core\transaction\TransactionBuilder;
use ontio\sdk\Wallet;
use ontio\crypto\PrivateKey;
use ontio\network\JsonRpc;
use ontio\crypto\Address;
use ontio\network\WebsocketRpc;
use ontio\smartcontract\abi\AbiInfo;
use ontio\smartcontract\abi\Parameter;
use ontio\smartcontract\abi\ParameterType;
use ontio\smartcontract\abi\Struct;
use ontio\common\ByteArray;
use ontio\smartcontract\data\IdContract;
use ontio\common\BigInt;

final class NeoVmTest extends TestCase
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

  public static $code;

  public static $abi;

  public static $codeHash;

  public static function setUpBeforeClass()
  {
    // pwd: 123456
    $walletData = '{"name":"MyWallet","version":"1.1","scrypt":{"p":8,"n":16384,"r":8,"dkLen":64},"accounts":[{"address":"ASSxYHNSsh4FdF2iNvHdh3Np2sgWU21hfp","enc-alg":"aes-256-gcm","key":"t2Kk2jNL4BAoXlYn309DKfxogxJRNvsJ8+GG4kiMB+UvWGXEilYRzfIYeNZbfVbu","algorithm":"ECDSA","salt":"CdRa1hTiOaVESNfJJmcMNw==","parameters":{"curve":"P-256"},"label":"","publicKey":"0344ea636caaebf23c7cec2219a75bd6260f891413467922975447ba57f3c824c6","signatureScheme":"SHA256withECDSA","isDefault":true,"lock":false},{"address":"AL9PtS6F8nue5MwxhzXCKaTpRb3yhtsix5","enc-alg":"aes-256-gcm","key":"vwIgX3qJO+1XikdPAfjAu/clsgS2l2xkEWsRR9XZQ8OyFViX+r/6Yq+cV0wnKQUM","algorithm":"SM2","salt":"xzvrFkHAgsEeX64V+4mpLw==","parameters":{"curve":"sm2p256v1"},"label":"","publicKey":"131403a9b89a0443ded240c3dee97221353d000d0dc905b7c085f4ef558b234a75e122","signatureScheme":"SM3withSM2","isDefault":false,"lock":false}]}';
    self::$wallet = Wallet::fromJson($walletData);
    self::$adminPrivateKey = self::$wallet->accounts[0]->exportPrivateKey('123456', self::$wallet->scrypt);
    self::$adminAddress = self::$wallet->accounts[0]->address;
    self::$gasLimit = '20000';
    self::$gasPrice = '0';

    self::$rpc = new JsonRpc('http://127.0.0.1:20336');

    self::$code = trim(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . "NeoVmTests.avm"));
    self::$abi = trim(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . "NeoVmTests.abi.json"));
    self::$codeHash = Address::fromVmCode(self::$code)->toHex();

    $txBuilder = new TransactionBuilder();
    $tx = $txBuilder->makeDeployCodeTransaction(
      self::$code,
      'name',
      '1.0',
      'alice',
      'mail',
      'desc',
      true,
      self::$gasPrice,
      '30000000',
      self::$adminAddress
    );
    $txBuilder->signTransaction($tx, self::$adminPrivateKey);
    $wsRpc = new WebsocketRpc('ws://127.0.0.1:20335');
    $res = $wsRpc->sendRawTransaction($tx->serialize(), false, true);
  }

  public function test_struct()
  {
    $abi = AbiInfo::fromJson(self::$abi);
    $fn = $abi->getFunction('testStructList');

    $struct = new Struct();
    $struct->add(100, ByteArray::fromBinary("claimid")->toHex());

    $txBuilder = new TransactionBuilder();
    $tx = $txBuilder->makeInvokeTransaction(
      $fn->name,
      [
        new Parameter("structList", ParameterType::Struct, $struct)
      ],
      new Address(self::$codeHash),
      '0',
      '30000000',
      self::$adminAddress
    );
    $txBuilder->signTransaction($tx, self::$adminPrivateKey);

    $res = self::$rpc->sendRawTransaction($tx->serialize(), true);
    $this->assertEquals('SUCCESS', $res->desc);

    $s = Struct::fromHex($res->result->Result);
    $int = BigInt::fromHex($s->list[0]->bytes->toHex());
    $str = $s->list[1]->bytes->toBinary();

    $this->assertTrue($int->equals(100));
    $this->assertEquals('claimid', $str);
  }

  public function test_setMap()
  {
    $abi = AbiInfo::fromJson(self::$abi);
    $fn = $abi->getFunction('testMap');

    $map = new Parameter("msg", ParameterType::Map, [
      "key" => new Parameter("", ParameterType::String, "value")
    ]);

    $txBuilder = new TransactionBuilder();
    $tx = $txBuilder->makeInvokeTransaction(
      $fn->name,
      [$map],
      new Address(self::$codeHash),
      '0',
      '30000000',
      self::$adminAddress
    );
    $txBuilder->signTransaction($tx, self::$adminPrivateKey);

    $res = self::$rpc->sendRawTransaction($tx->serialize(), false, true);
    $this->assertEquals('SUCCESS', $res->desc);
  }

  public function test_getMap()
  {
    $abi = AbiInfo::fromJson(self::$abi);
    $fn = $abi->getFunction('testGetMap');

    $txBuilder = new TransactionBuilder();
    $tx = $txBuilder->makeInvokeTransaction(
      $fn->name,
      [
        new Parameter("key", ParameterType::String, "key")
      ],
      new Address(self::$codeHash),
      '0',
      '30000000',
      self::$adminAddress
    );
    $txBuilder->signTransaction($tx, self::$adminPrivateKey);

    $res = self::$rpc->sendRawTransaction($tx->serialize(), true);
    $this->assertEquals('SUCCESS', $res->desc);
    $this->assertEquals('value', ByteArray::fromHex($res->result->Result)->toBinary());
  }

  public function test_setMapInMap()
  {
    $abi = AbiInfo::fromJson(self::$abi);
    $fn = $abi->getFunction('testMapInMap');

    $map = new Parameter("msg", ParameterType::Map, [
      "key" => new Parameter("", ParameterType::Map, [
        "key" => new Parameter("", ParameterType::String, "value")
      ])
    ]);

    $txBuilder = new TransactionBuilder();
    $tx = $txBuilder->makeInvokeTransaction(
      $fn->name,
      [$map],
      new Address(self::$codeHash),
      '0',
      '30000000',
      self::$adminAddress
    );
    $txBuilder->signTransaction($tx, self::$adminPrivateKey);

    $res = self::$rpc->sendRawTransaction($tx->serialize(), false, true);
    $this->assertEquals('SUCCESS', $res->desc);
  }

  public function test_getMapInMap()
  {
    $abi = AbiInfo::fromJson(self::$abi);
    $fn = $abi->getFunction('testGetMapInMap');

    $txBuilder = new TransactionBuilder();
    $tx = $txBuilder->makeInvokeTransaction(
      $fn->name,
      [
        new Parameter("key", ParameterType::String, "key")
      ],
      new Address(self::$codeHash),
      '0',
      '30000000',
      self::$adminAddress
    );
    $txBuilder->signTransaction($tx, self::$adminPrivateKey);

    $res = self::$rpc->sendRawTransaction($tx->serialize(), true);
    $this->assertEquals('SUCCESS', $res->desc);
    $this->assertEquals('value', ByteArray::fromHex($res->result->Result)->toBinary());
  }
}
