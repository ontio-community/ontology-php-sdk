<?php

use PHPUnit\Framework\TestCase;
use ontio\network\JsonRpc;
use ontio\crypto\PrivateKey;
use ontio\crypto\PublicKey;
use ontio\sdk\Account;
use ontio\crypto\Address;

final class RpcTest extends TestCase
{
  /** @var JsonRpc */
  public static $rpc;
  /** @var PrivateKey */
  public static $priKey;
  /** @var PublicKey */
  public static $pubKey;
  /** @var Account */
  public static $account;
  /** @var Address */
  public static $address;

  public static function setUpBeforeClass()
  {
    self::$rpc = new JsonRpc('http://127.0.0.1:20336');
    self::$priKey = PrivateKey::random();
    self::$pubKey = self::$priKey->getPublicKey();
    self::$account = Account::create('123456', self::$priKey);
    self::$address = self::$account->address;
  }

  public function test_block_height()
  {
    $h = self::$rpc->getBlockHeight();
    $this->assertEquals('SUCCESS', $h->desc);
  }

  public function test_get_balance()
  {
    $b = self::$rpc->getBalance(self::$address);
    $this->assertEquals('SUCCESS', $b->desc);
  }

  public function test_get_node_count()
  {
    $c = self::$rpc->getNodeCount();
    $this->assertEquals('SUCCESS', $c->desc);
  }

  public function test_get_unclaimed_ong()
  {
    $c = self::$rpc->getUnclaimedOng(new Address('ASSxYHNSsh4FdF2iNvHdh3Np2sgWU21hfp'));
    $this->assertEquals('SUCCESS', $c->desc);
  }
}
