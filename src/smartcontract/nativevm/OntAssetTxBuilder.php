<?php

namespace ontio\smartcontract\nativevm;

use ontio\sdk\Constant;
use ontio\crypto\Address;
use \GMP;
use ontio\smartcontract\abi\Struct;
use ontio\smartcontract\abi\NativeVmParamsBuilder;
use ontio\core\transaction\TransactionBuilder;
use ontio\core\transaction\Transfer;
use ontio\core\transaction\Transaction;
use ontio\core\scripts\ScriptReader;
use ontio\common\ByteArray;
use ontio\common\BigInt;

class OntAssetTxBuilder
{
  const ONT_CONTRACT = '0000000000000000000000000000000000000001';
  const ONG_CONTRACT = '0000000000000000000000000000000000000002';

  public function getTokenContract(string $tokenType)
  {
    if ($tokenType === Constant::$TOKEN_TYPE->get('ONT')) {
      return new Address(self::ONT_CONTRACT);
    } else if ($tokenType === Constant::$TOKEN_TYPE->get('ONG')) {
      return new Address(self::ONG_CONTRACT);
    }
    throw new \InvalidArgumentException('Error token type');
  }

  /**
   * @param string|int|GMP $amount
   * @return boolean
   */
  public function verifyAmount($amount)
  {
    $amount = ($amount instanceof GMP) ? $amount : gmp_init((int)$amount);
    if (!(gmp_cmp($amount, 0) > 0)) {
      throw new \InvalidArgumentException('Amount is invalid');
    }
    return $amount;
  }

  public function makeTransferTx(
    string $tokenType,
    Address $from,
    Address $to,
    $amount,
    string $gasPrice = '',
    string $gasLimit = '',
    ? Address $payer = null
  ) : Transfer {

    $num = $this->verifyAmount($amount);

    $struct = new Struct();
    $struct->add($from, $to, $num);
    $list = [[$struct]];

    $builder = new NativeVmParamsBuilder();
    $builder->pushNativeCodeScript($list);
    $params = $builder->toHex();

    $contract = $this->getTokenContract($tokenType);
    $txBuilder = new TransactionBuilder();
    /** @var Transfer $tx */
    $tx = $txBuilder->makeNativeContractTx('transfer', $params, $contract, $gasPrice, $gasLimit, $payer);
    $tx->tokenType = $tokenType;
    $tx->from = $form;
    $tx->to = $to;
    $tx->amount = $amount;
    $tx->method = 'transfer';

    if ($payer) {
      $tx->payer = $payer;
    } else {
      $tx->payer = $from;
    }
    return $tx;
  }

  public function makeWithdrawOngTx(
    Address $from,
    Address $to,
    $amount,
    string $gasPrice,
    string $gasLimit,
    Address $payer
  ) : Transfer {

    $num = $this->verifyAmount($amount);

    $struct = new Struct();
    $struct->add($from, new Address(self::ONT_CONTRACT), $to, $num);
    $list = [$struct];

    $builder = new NativeVmParamsBuilder();
    $builder->pushNativeCodeScript($list);
    $params = $builder->toHex();

    $txBuilder = new TransactionBuilder();
    /** @var Transfer $tx */
    $tx = $txBuilder->makeNativeContractTx('transferFrom', $params, new Address(self::ONG_CONTRACT), $gasPrice, $gasLimit, $payer);
    $tx->tokenType = 'ONG';
    $tx->from = $from;
    $tx->to = $to;
    $tx->amount = $amount;
    $tx->method = 'transferFrom';
    return $tx;
  }

  /**
   * @param ScriptReader $r
   * @return Transfer|Transaction
   */
  public function deserializeTransferTx(ScriptReader $r)
  {
    /** @var Transfer $tx */
    $tx = Transaction::deserialize($r);
    $code = $tx->payload->serialize();
    $contractIdx1 = strrpos($code, '14' . '000000000000000000000000000000000000000');
    $contractIdx2 = strrpos($code, '14' . '0000000000000000000000000000000000000002');

    if ($contractIdx1 > 0 && substr($code, $contractIdx1 + 41, 1) === '1') {
      $tx->tokenType = 'ONT';
    } else if ($contractIdx1 > 0 && substr($code, $contractIdx1 + 41, 1) === '2') {
      $tx->tokenType = 'ONG';
    } else {
      throw new \InvalidArgumentException('Not a transfer tx');
    }

    $contractIdx = max($contractIdx1, $contractIdx2);
    $params = substr($code, 0, $contractIdx);
    $paramsEnd = strpos($params, '6a7cc86c') + 8;
    if (substr($params, $paramsEnd, 4) === '51c1') {
      $methodStr = substr($params, $paramsEnd + 6, strlen($params) - $paramsEnd - 6);
      $tx->method = ByteArray::fromHex($methodStr)->toBinary();
    } else {
      $methodStr = substr($params, $paramsEnd + 2, strlen($params) - $paramsEnd - 2);
      $tx->method = ByteArray::fromHex($methodStr)->toBinary();
    }

    $r = new ScriptReader(ByteArray::fromHex($params));
    if ($tx->method === 'transfer') {
      $r->advance(5);
      $tx->from = new Address($r->forward(20)->toHex());
      $r->advance(4);
      $tx->to = new Address($r->forward(20)->toHex());
      $r->advance(3);
      $numTmp = $r->readUInt8();
      if ($r->branch($r->offset())->forward(3)->toHex() === '6a7cc8') {
        $tx->amount = $numTmp - 80;
      } else {
        $tx->amount = gmp_strval(BigInt::fromHex($r->forward($numTmp)->toHex())->value);
      }
    } else if ($tx->method === 'transferFrom') {
      $r->advance(5);
      $tx->from = new Address($r->forward(20));
      $r->advance(28);
      $tx->to = new Address($r->forward(20));
      $r->advance(3);
      $numTmp = $r->readUInt8();
      if ($r->branch($r->offset())->forward(3)->toHex() === '6a7cc8') {
        $tx->amount = $numTmp - 80;
      } else {
        $tx->amount = gmp_strval(BigInt::fromHex($r->forward($numTmp)->toHex())->value);
      }
    } else {
      throw new \InvalidArgumentException('Not a transfer tx');
    }

    return $tx;
  }
}
