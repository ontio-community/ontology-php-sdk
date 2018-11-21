<?php

namespace ontio\network;

use ontio\sdk\Constant;
use Ratchet\Client\WebSocket;
use function Ratchet\Client\connect;
use function Clue\React\Block\await;
use React\Promise\Deferred;
use Ratchet\RFC6455\Messaging\Message;
use React\Promise\PromiseInterface;

class WebsocketRpc
{
  /** @var string */
  public $url;

  public $loop;

  public $conn;

  public function __construct(string $url = '')
  {
    $this->url = $url === '' ? Constant::$TEST_ONT_URL->get('SOCKET_URL') : $url;
    $this->loop = \React\EventLoop\Factory::create();
  }

  public function newConn() : WebSocket
  {
    return await(connect($this->url, [], [], $this->loop), $this->loop);
  }

  public function sendRequest(array $data) : WebsocketRpcResult
  {
    $deferred = new Deferred();

    $conn = $this->newConn();
    $id = uniqid();

    $cb = function ($msg) use ($deferred, $conn, $id) {
      /** @var Message $msg */
      $msg = WebsocketRpcResult::fromJson(json_decode($msg->getPayload()));
      if ($msg->Id === $id) {
        $deferred->resolve($msg);
        $conn->close();
      }
    };
    $conn->on('message', $cb);

    $data['Id'] = $id;
    $conn->send(json_encode($data));

    return await($deferred->promise(), $this->loop);
  }

  public function send(array $data, callable $filter, int $timeout = 60)
  {
    $deferred = new Deferred();

    $conn = $this->newConn();
    $id = uniqid();

    $txHash;
    $cb = function ($msg) use ($deferred, $conn, $id, &$txHash, $filter) {
      /** @var Message $msg */
      $msg = WebsocketRpcResult::fromJson(json_decode($msg->getPayload()));
      if ($msg->Id === $id) {
        $txHash = $msg->Result;
      } else {
        $filter($deferred, $conn, $id, $txHash, $msg);
      }
    };
    $conn->on('message', $cb);

    $data['Id'] = $id;
    $conn->send(json_encode($data));

    return await($deferred->promise(), $this->loop, $timeout);
  }

  public function sendRawTransaction(string $data, bool $preExec = false, $waitNotify = false, int $timeout = 60) : WebsocketRpcResult
  {
    $data = [
      'Action' => 'sendrawtransaction',
      'Version' => '1.0.0',
      'Data' => $data
    ];

    if ($preExec) {
      $data['PreExec'] = '1';
    }

    if (!$waitNotify) {
      return $this->sendRequest($data);
    }

    $filter = function ($deferred, $conn, $id, $txHash, $msg) {
      if ($msg->Id === $id) {
        $txHash = $msg->Result;
      } else if ($txHash !== null && $msg->Action === 'Notify' && $msg->Result->TxHash === $txHash) {
        $deferred->resolve($msg);
        $conn->close();
      }
    };

    return $this->send($data, $filter, $timeout);
  }

  public function getNodeCount()
  {
    $data = [
      'Action' => 'getconnectioncount',
      'Version' => '1.0.0'
    ];
    return $this->sendRequest($data);
  }
}
