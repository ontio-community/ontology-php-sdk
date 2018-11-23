<?php

namespace ontio\smartcontract\neovm;

use ontio\crypto\Address;

use ontio\core\transaction\Transaction;
use ontio\core\transaction\TransactionBuilder;
use ontio\smartcontract\abi\Parameter;
use ontio\smartcontract\abi\ParameterType;
use ontio\common\ByteArray;
use ontio\smartcontract\abi\NativeVmParamsBuilder;

class Oep4TxBuilder
{
  public static $fnNames;

  /** @var Address */
  public $contractAddr;

  public function __construct(Address $contractAddr)
  {
    $this->contractAddr = $contractAddr;
  }

  public function makeInitTx(string $gasPrice, string $gasLimit, ? Address $payer = null) : Transaction
  {
    $builder = new TransactionBuilder();
    $fn = self::$fnNames->Init;
    return $builder->makeInvokeTransaction($fn, [], $this->contractAddr, $gasPrice, $gasLimit, $payer);
  }

  public function makeTransferTx(
    Address $from,
    Address $to,
    string $amount,
    string $gasPrice,
    string $gasLimit,
    Address $payer
  ) : Transaction {
    $builder = new TransactionBuilder();
    $fn = self::$fnNames->Transfer;
    $p1 = new Parameter('from', ParameterType::ByteArray, $from->serialize());
    $p2 = new Parameter('to', ParameterType::ByteArray, $to->serialize());
    $p3 = new Parameter('value', ParameterType::Long, $amount);
    return $builder->makeInvokeTransaction($fn, [$p1, $p2, $p3], $this->contractAddr, $gasPrice, $gasLimit, $payer);
  }

  /**
   *
   * @param OepState[] $states
   * @param string $gasPrice
   * @param string $gasLimit
   * @param Address $payer
   * @return Transaction
   */
  public function makeTransferMultiTx(array $states, string $gasPrice, string $gasLimit, Address $payer) : Transaction
  {
    $fn = self::$fnNames->TransferMulti;
    $list = [ByteArray::fromBinary($fn)->toHex()];
    $args = [];
    foreach ($states as $state) {
      $args[] = [$state->from, $state->to, $state->amount];
    }
    $list[] = $args;
    $paramsBuilder = new NativeVmParamsBuilder();
    $params = $paramsBuilder->pushCodeParams($list)->toHex();
    $txBuilder = new TransactionBuilder();
    return $txBuilder->makeInvokeTransaction('', $params, $this->contractAddr, $gasPrice, $gasLimit, $payer);
  }

  public function makeApproveTx(
    Address $owner,
    Address $spender,
    string $amount,
    string $gasPrice,
    string $gasLimit,
    Address $payer
  ) : Transaction {
    $fn = self::$fnNames->Approve;
    $params = [
      new Parameter('owner', ParameterType::ByteArray, $owner->serialize()),
      new Parameter('spender', ParameterType::ByteArray, $spender->serialize()),
      new Parameter('amount', ParameterType::Long, $amount),
    ];
    $builder = new TransactionBuilder();
    return $builder->makeInvokeTransaction($fn, $params, $this->contractAddr, $gasPrice, $gasLimit, $payer);
  }

  public function makeTransferFromTx(
    Address $spender,
    Address $from,
    Address $to,
    string $amount,
    string $gasPrice,
    string $gasLimit,
    Address $payer
  ) : Transaction {
    $fn = self::$fnNames->TransferFromm;
    $params = [
      new Parameter('spender', ParameterType::ByteArray, $spender->serialize()),
      new Parameter('from', ParameterType::ByteArray, $from->serialize()),
      new Parameter('to', ParameterType::ByteArray, $to->serialize()),
      new Parameter('amount', ParameterType::Long, $amount),
    ];
    $builder = new TransactionBuilder();
    return $builder->makeInvokeTransaction($fn, $params, $this->contractAddr, $gasPrice, $gasLimit, $payer);
  }

  public function makeQueryAllowanceTx(
    Address $owner,
    Address $spender
  ) : Transaction {
    $fn = self::$fnNames->Allowance;
    $params = [
      new Parameter('owner', ParameterType::ByteArray, $owner->serialize()),
      new Parameter('spender', ParameterType::ByteArray, $spender->serialize()),
    ];
    $builder = new TransactionBuilder();
    return $builder->makeInvokeTransaction($fn, $params, $this->contractAddr);
  }

  public function makeQueryBalanceOfTx(Address $addr) : Transaction
  {
    $fn = self::$fnNames->BalanceOf;
    $p = new Parameter('from', ParameterType::ByteArray, $addr->serialize());
    $builder = new TransactionBuilder();
    return $builder->makeInvokeTransaction($fn, [$p], $this->contractAddr);
  }

  public function makeQueryTotalSupplyTx() : Transaction
  {
    $fn = self::$fnNames->TotalSupply;
    $builder = new TransactionBuilder();
    return $builder->makeInvokeTransaction($fn, [], $this->contractAddr);
  }

  public function makeQueryDecimalsTx() : Transaction
  {
    $fn = self::$fnNames->Decimals;
    $builder = new TransactionBuilder();
    return $builder->makeInvokeTransaction($fn, [], $this->contractAddr);
  }

  public function makeQuerySymbolTx() : Transaction
  {
    $fn = self::$fnNames->Symbol;
    $builder = new TransactionBuilder();
    return $builder->makeInvokeTransaction($fn, [], $this->contractAddr);
  }

  public function makeQueryNameTx() : Transaction
  {
    $fn = self::$fnNames->Name;
    $builder = new TransactionBuilder();
    return $builder->makeInvokeTransaction($fn, [], $this->contractAddr);
  }
}

Oep4TxBuilder::$fnNames = (object)[
  'Init' => 'init',
  'Transfer' => 'transfer',
  'TransferMulti' => 'transferMulti',
  'Approve' => 'approve',
  'TransferFromm' => 'transferFrom',
  'Allowance' => 'allowance',
  'BalanceOf' => 'balanceOf',
  'TotalSupply' => 'totalSupply',
  'Symbol' => 'symbol',
  'Decimals' => 'decimals',
  'Name' => 'name'
];
