<?php

use PHPUnit\Framework\TestCase;

use ontio\sdk\Wallet;
use ontio\crypto\PrivateKey;
use ontio\sdk\Account;

final class WalletTest extends TestCase
{
  /** @var Wallet */
  public static $w;
  /** @var string */
  public static $wStr;

  public static function setUpBeforeClass()
  {
    $pk = PrivateKey::random();
    self::$w = Wallet::create('mickey');
    self::$wStr = json_encode(self::$w);
  }

  public function test_wallet_ok()
  {
    $this->assertTrue(!empty(self::$wStr));
  }

  public function test_add_account()
  {
    $pk = PrivateKey::random();
    $acc = Account::create('123456', $pk, 'mickey');
    self::$w->addAccount($acc);
    $this->assertTrue(count(self::$w->accounts) === 1);
  }

  public function test_from_json()
  {
    $jsonStr = '{"name":"MyWallet","version":"1.1","scrypt":{"p":8,"n":16384,"r":8,"dkLen":64},"accounts":[{"address":"AUr5QUfeBADq6BMY6Tp5yuMsUNGpsD7nLZ","enc-alg":"aes-256-gcm","key":"KysbyR9wxnD2XpiH5Xgo4q0DTqKJxaA+Sz3I60fIvsn7wktC9Utb1XYzfHt4mjjl","algorithm":"ECDSA","salt":"dg2t+nlEDEvhP52epby/gw==","parameters":{"curve":"P-256"},"label":"","publicKey":"03f631f975560afc7bf47902064838826ec67794ddcdbcc6f0a9c7b91fc8502583","signatureScheme":"SHA256withECDSA","isDefault":true,"lock":false}]}';
    $w = Wallet::fromJson($jsonStr);
    $this->assertTrue(count($w->accounts) === 1);
    $this->assertTrue(empty($w->identities));
  }
}
