<?php

namespace ontio\transaction;

use ontio\crypto\Address;
use ontio\common\ByteArray;
use ontio\Constant;
use ontio\transaction\payload\InvokeCode;
use ontio\common\Fixed64;
use ontio\crypto\PrivateKey;
use ontio\crypto\SignatureScheme;


class TransactionBuilder
{

  public function signTransaction(Transaction $tx, PrivateKey $prikey, ? SignatureScheme $scheme = null)
  {
    $sig = TxSignature::create($tx, $prikey, $scheme);
    $tx->sigs = [$sig];
  }

  /**
   * Creates transaction to invoke native contract
   *
   * @param string $fnName Function name of contract to call
   * @param string $params Parameters serialized in hex string
   * @param Address $contractAddr Address of contract
   * @param string $gasPrice Gas price
   * @param string $gasLimit Gas limit
   * @param Address $payer Address to pay for transaction gas
   * @return Transaction|Transfer
   */
  public function makeNativeContractTx(
    string $fnName,
    string $params,
    Address $contractAddr,
    string $gasPrice = '',
    string $gasLimit = '',
    ? Address $payer = null
  ) : Transaction {
    $builder = new ScriptBuilder();
    $builder->pushArray(ByteArray::fromHex($params));
    $builder->pushHexString(ByteArray::fromBinary($fnName)->toHex());
    $builder->pushHexString($contractAddr->serialize());
    $builder->pushNum(0);
    $builder->pushInt(Opcode::SYSCALL);
    $builder->pushHexString(ByteArray::fromBinary(Constant::$NATIVE_INVOKE_NAME)->toHex());
    $payload = new InvokeCode();
    $payload->code = $builder->toHex();

    $tx;
    if ($fnName === 'transfer' || $fnName === 'transferFrom') {
      $tx = new Transfer();
    } else {
      $tx = new Transaction();
    }

    $tx->type = TxType::Invoke;
    $tx->payload = $payload;
    if ($gasLimit !== '') {
      $tx->gasLimit = new Fixed64((int)$gasLimit);
    }
    if ($gasPrice !== '') {
      $tx->gasPrice = new Fixed64((int)$gasPrice);
    }
    if ($payer) {
      $tx->payer = $payer;
    }
    return $tx;
  }
}
