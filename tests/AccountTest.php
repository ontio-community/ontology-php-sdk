<?php

use PHPUnit\Framework\TestCase;

use ontio\crypto\PrivateKey;
use ontio\Account;
use ontio\ErrorCode;
use ontio\common\ByteArray;
use ontio\crypto\Address;

final class AccountTest extends TestCase
{
  /**
   * @var PrivateKey
   */
  protected static $priKey;
  /**
   * @var string
   */
  protected static $accDataStr;
  /**
   * @var Account
   */
  protected static $acc;
  /**
   * @var PrivateKey
   */
  protected static $encPriKey;

  public static function setUpBeforeClass()
  {
    self::$priKey = PrivateKey::random();
  }

  public function test_create()
  {
    self::$acc = Account::create('123456', self::$priKey, 'mickey');
    self::$encPriKey = self::$acc->encryptedKey;
    self::$accDataStr = json_encode(self::$acc);

    $this->assertTrue(!empty(self::$accDataStr));
  }

  public function test_import_with_pwd()
  {
    $acc = Account::import('mickey', self::$encPriKey, '123456', self::$acc->address, self::$acc->salt);
    $pk = $acc->exportPrivateKey('123456');
    $this->assertEquals(self::$priKey->key->toHex(), $pk->key->toHex());
  }

  public function test_import_with_invalid_pwd()
  {
    try {
      Account::import('mickey', self::$encPriKey, '1234567', self::$acc->address, self::$acc->salt);
    } catch (\Exception $e) {
      $this->assertEquals(ErrorCode::DECRYPT_ERROR, (int)$e->getMessage());
    }
  }
}
