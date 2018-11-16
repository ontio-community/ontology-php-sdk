<?php

namespace ontio\network;

use ontio\sdk\Constant;
use ontio\crypto\Address;
use GuzzleHttp\Client;
use ontio\sdk\ErrorCode;

class JsonRpc
{
  /** @var string */
  public $url;


  /** @var Client */
  public $c;

  public function __construct(string $url = '')
  {
    $this->url = $url === '' ? Constant::$TEST_ONT_URL->get('RPC_URL') : $url;
    $this->c = new Client();
  }

  public function getUrl() : string
  {
    return $this->url;
  }

  public function makeRequest(string $method, ...$params)
  {
    return [
      'jsonrpc' => '2.0',
      'method' => $method,
      'params' => $params,
      'id' => 1
    ];
  }

  public function req(string $method, ...$params)
  {
    $req = $this->makeRequest($method, ...$params);
    $resp = $this->c->request('POST', $this->url, ['json' => $req])->getBody()->getContents();
    return JsonRpcResult::fromJson(json_decode($resp));
  }

  public function getBalance(Address $address) : JsonRpcResult
  {
    return $this->req('getbalance', $address->toBase58());
  }

  /**
   * Send ran transaction to blockchain.
   *
   * @param string Hex encoded data.
   * @param boolean Decides if it is a pre-execute transaction.
   * @return JsonRpcResult
   */
  public function sendRawTransaction(string $data, bool $preExec = false) : JsonRpcResult
  {
    return $preExec ? $this->req('sendrawtransaction', $data, 1) : $this->req('sendrawtransaction', $data);
  }

  public function getRawTransaction(string $txHash) : JsonRpcResult
  {
    return $this->req('getrawtransaction', $txHash);
  }

  public function getRawTransactionJson(string $txHash) : JsonRpcResult
  {
    return $this->req('getrawtransaction', $txHash, 1);
  }

  public function getNodeCount() : JsonRpcResult
  {
    return $this->req('getconnectioncount');
  }

  public function getBlockHeight() : JsonRpcResult
  {
    return $this->req('getblockcount');
  }

  public function getBlockCount() : JsonRpcResult
  {
    return $this->req('getblockcount');
  }

  /**
   * Get block info by block's height or hash.
   * The result is json.
   *
   * @param string|int $value
   * @return JsonRpcResult
   */
  public function getBlockJson($value) : JsonRpcResult
  {
    return $this->req('getblock', $value, 1);
  }

  /**
   * Get contract info by contract' code hash.
   * The result is hex encoded string.
   *
   * @param string $hash
   * @return JsonRpcResult
   */
  public function getContract(string $hash) : JsonRpcResult
  {
    return $this->req('getcontractstate', $hash);
  }

  public function getContractJson(string $hash) : JsonRpcResult
  {
    return $this->req('getcontractstate', $hash, 1);
  }

  /**
   * Get block info by block's height or hash.
   * The result is hex encoded string.
   *
   * @param string|int $value Block's height or hash
   * @return JsonRpcResult
   */
  public function getBlock($value) : JsonRpcResult
  {
    return $this->req('getblock', $value);
  }

  public function getSmartCodeEvent($value) : JsonRpcResult
  {
    return $this->req('getsmartcodeevent', $value);
  }

  public function getBlockHeightByTxHash($txHash) : JsonRpcResult
  {
    return $this->req('getblockheightbytxhash', $txHash);
  }

  public function getStorage(string $codeHash, string $key) : JsonRpcResult
  {
    return $this->req('getstorage', $codeHash, $key);
  }

  public function getMerkleProof(string $hash) : JsonRpcResult
  {
    return $this->req('getmerkleproof', $hash);
  }

  public function getAllowance(string $asset, Address $from, Address $to) : JsonRpcResult
  {
    if ($asset !== 'ont' && $asset !== 'ont') {
      throw new \InvalidArgumentException(ErrorCode::INVALID_PARAMS);
    }
    return $this->req('getallowance', $asset, $from->toBase58(), $to->toBase58());
  }
}
