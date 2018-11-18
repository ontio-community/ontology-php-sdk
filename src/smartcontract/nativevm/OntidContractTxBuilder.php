<?php

namespace ontio\smartcontract\nativevm;

use ontio\crypto\PublicKey;
use ontio\crypto\Address;
use ontio\core\transaction\Transaction;
use ontio\common\ByteArray;
use ontio\smartcontract\abi\NativeVmParamsBuilder;
use ontio\core\transaction\TransactionBuilder;
use ontio\smartcontract\abi\Struct;

class OntidContractTxBuilder
{
  const ONTID_CONTRACT = '0000000000000000000000000000000000000003';
  public static $ONTID_METHOD;

  public function buildRegisterOntidTx(
    string $ontid,
    PublicKey $pubkey,
    string $gasPrice,
    string $gasLimit,
    ? Address $payer = null
  ) : Transaction {
    $method = self::$ONTID_METHOD->regIDWithPublicKey;

    if (strpos($ontid, 'did') === 0) {
      $ontid = ByteArray::fromBinary($ontid)->toHex();
    }

    $struct = new Struct();
    $struct->add($ontid, $pubkey->toHex());
    $list = [$struct];

    $paramsBuilder = new NativeVmParamsBuilder();
    $paramsBuilder->pushNativeCodeScript($list);
    $params = $paramsBuilder->toHex();

    $txBuilder = new TransactionBuilder();
    return $txBuilder->makeNativeContractTx($method, $params, new Address(self::ONTID_CONTRACT), $gasPrice, $gasLimit, $payer);
  }

  public function buildGetDDOTx(string $ontid) : Transaction
  {
    $method = self::$ONTID_METHOD->getDDO;

    if (strpos($ontid, 'did') === 0) {
      $ontid = ByteArray::fromBinary($ontid)->toHex();
    }

    $struct = new Struct();
    $struct->add($ontid);

    $paramsBuilder = new NativeVmParamsBuilder();
    $paramsBuilder->pushNativeCodeScript([$struct]);
    $params = $paramsBuilder->toHex();

    $txBuilder = new TransactionBuilder();
    return $txBuilder->makeNativeContractTx($method, $params, new Address(self::ONTID_CONTRACT));
  }
}

OntidContractTxBuilder::$ONTID_METHOD = (object)[
  'regIDWithPublicKey' => 'regIDWithPublicKey',
  'regIDWithAttributes' => 'regIDWithAttributes',
  'addAttributes' => 'addAttributes',
  'removeAttribute' => 'removeAttribute',
  'getAttributes' => 'getAttributes',
  'getDDO' => 'getDDO',
  'addKey' => 'addKey',
  'removeKey' => 'removeKey',
  'getPublicKeys' => 'getPublicKeys',
  'addRecovery' => 'addRecovery',
  'changeRecovery' => 'changeRecovery',
  'getKeyState' => 'getKeyState'
];
