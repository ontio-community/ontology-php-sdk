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

    self::$code = '57c56b6c766b00527ac46c766b51527ac4616c766b00c307546573744d6170876c766b52527ac46c766b52c3641200616165c7006c766b53527ac462b4006c766b00c30e446573657269616c697a654d6170876c766b54527ac46c766b54c3641900616c766b51c300c3616511026c766b53527ac4627a006c766b00c30a54657374537472756374876c766b55527ac46c766b55c3641200616165e9026c766b53527ac4624b006c766b00c311446573657269616c697a65537472756374876c766b56527ac46c766b56c3641900616c766b51c300c36165cc036c766b53527ac4620e00006c766b53527ac46203006c766b53c3616c756658c56b6161681953797374656d2e53746f726167652e476574436f6e746578746c766b00527ac4c76c766b51527ac401646c766b52527ac46c766b51c3036b65796c766b52c3c4616c766b51c361681853797374656d2e52756e74696d652e53657269616c697a656c766b53527ac46c766b00c30274786c766b53c3615272681253797374656d2e53746f726167652e507574616c766b00c3027478617c681253797374656d2e53746f726167652e4765746c766b54527ac46c766b54c361681a53797374656d2e52756e74696d652e446573657269616c697a656c766b55527ac46c766b55c36416006c766b55c3036b6579c36c766b52c39c620400006c766b56527ac46c766b56c3643c00616c766b00c306726573756c740474727565615272681253797374656d2e53746f726167652e507574616c766b53c36c766b57527ac46238006c766b00c306726573756c740566616c7365615272681253797374656d2e53746f726167652e50757461006c766b57527ac46203006c766b57c3616c756656c56b6c766b00527ac4616c766b00c361681a53797374656d2e52756e74696d652e446573657269616c697a656c766b51527ac461681953797374656d2e53746f726167652e476574436f6e746578746c766b52527ac401646c766b53527ac46c766b51c36416006c766b51c3036b6579c36c766b53c39c620400006c766b54527ac46c766b54c3643800616c766b52c306726573756c740474727565615272681253797374656d2e53746f726167652e50757461516c766b55527ac46241006c766b52c306726573756c740566616c7365615272681253797374656d2e53746f726167652e507574616c766b51c3036b6579c36c766b55527ac46203006c766b55c3616c756656c56b6161681953797374656d2e53746f726167652e476574436f6e746578746c766b00527ac46152c56c766b51527ac46c766b51c307636c61696d6964517cc46c766b51c30164007cc46c766b51c361681853797374656d2e52756e74696d652e53657269616c697a656c766b52527ac46c766b00c30274786c766b52c3615272681253797374656d2e53746f726167652e507574616c766b00c3027478617c681253797374656d2e53746f726167652e4765746c766b53527ac46c766b52c300a06c766b54527ac46c766b54c3641300616c766b52c36c766b55527ac46238006c766b00c306726573756c740566616c7365615272681253797374656d2e53746f726167652e50757461006c766b55527ac46203006c766b55c3616c756656c56b6c766b00527ac4616c766b00c361681a53797374656d2e52756e74696d652e446573657269616c697a656c766b51527ac461681953797374656d2e53746f726167652e476574436f6e746578746c766b52527ac401646c766b53527ac46c766b51c36413006c766b51c300c36c766b53c39c620400006c766b54527ac46c766b54c3643800616c766b52c306726573756c740474727565615272681253797374656d2e53746f726167652e50757461516c766b55527ac4623e006c766b52c306726573756c740566616c7365615272681253797374656d2e53746f726167652e507574616c766b51c300c36c766b55527ac46203006c766b55c3616c7566';
    self::$abi = '{"hash":"0x3c341335540c51c03bdef0f460994f99ea4659e8","entrypoint":"Main","functions":[{"name":"Main","parameters":[{"name":"operation","type":"String"},{"name":"args","type":"Array"}],"returntype":"Any"},{"name":"TestMap","parameters":[],"returntype":"Any"},{"name":"DeserializeMap","parameters":[{"name":"param","type":"ByteArray"}],"returntype":"Any"},{"name":"TestStruct","parameters":[],"returntype":"Any"},{"name":"DeserializeStruct","parameters":[{"name":"param","type":"ByteArray"}],"returntype":"Any"}],"events":[]}';
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

  public function test_TestMap()
  {
    $abi = AbiInfo::fromJson(self::$abi);
    $fn = $abi->getFunction('TestMap');

    $txBuilder = new TransactionBuilder();
    $tx = $txBuilder->makeInvokeTransaction(
      $fn->name,
      $fn->parameters,
      new Address(self::$codeHash),
      '0',
      '30000000',
      self::$adminAddress
    );
    $txBuilder->signTransaction($tx, self::$adminPrivateKey);

    $res = self::$rpc->sendRawTransaction($tx->serialize());
    $this->assertEquals('SUCCESS', $res->desc);
  }

  public function test_DeserializeMap()
  {
    $abi = AbiInfo::fromJson(self::$abi);
    $fn = $abi->getFunction('DeserializeMap');

    $map = [
      'key' => new Parameter('', ParameterType::Integer, 100)
    ];

    $param = new Parameter('param', ParameterType::ByteArray, $map);
    $fn->setParamsValue($param);

    $txBuilder = new TransactionBuilder();
    $tx = $txBuilder->makeInvokeTransaction(
      $fn->name,
      $fn->parameters,
      new Address(self::$codeHash),
      '0',
      '30000000',
      self::$adminAddress
    );

    $txBuilder->signTransaction($tx, self::$adminPrivateKey);

    $res = self::$rpc->sendRawTransaction($tx->serialize());
    $this->assertEquals('SUCCESS', $res->desc);
  }

  public function test_TestStruct()
  {
    $abi = AbiInfo::fromJson(self::$abi);
    $fn = $abi->getFunction('TestStruct');

    $txBuilder = new TransactionBuilder();
    $tx = $txBuilder->makeInvokeTransaction(
      $fn->name,
      $fn->parameters,
      new Address(self::$codeHash),
      '0',
      '30000000',
      self::$adminAddress
    );
    $txBuilder->signTransaction($tx, self::$adminPrivateKey);

    $res = self::$rpc->sendRawTransaction($tx->serialize());
    $this->assertEquals('SUCCESS', $res->desc);
  }

  public function test_DeserializeStruct()
  {
    $abi = AbiInfo::fromJson(self::$abi);
    $fn = $abi->getFunction('DeserializeStruct');

    $struct = new Struct();
    $struct->add(
      100,
      ByteArray::fromBinary('claimid')->toHex()
    );

    $param = new Parameter($fn->parameters[0]->getName(), ParameterType::ByteArray, $struct);
    $fn->setParamsValue($param);

    $txBuilder = new TransactionBuilder();
    $tx = $txBuilder->makeInvokeTransaction(
      $fn->name,
      $fn->parameters,
      new Address(self::$codeHash),
      '0',
      '30000000',
      self::$adminAddress
    );

    $txBuilder->signTransaction($tx, self::$adminPrivateKey);

    $res = self::$rpc->sendRawTransaction($tx->serialize());
    $this->assertEquals('SUCCESS', $res->desc);
  }
}
