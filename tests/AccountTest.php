<?php

use PHPUnit\Framework\TestCase;

use ontio\crypto\PrivateKey;
use ontio\sdk\Account;
use ontio\core\ErrorCode;
use ontio\common\ByteArray;
use ontio\crypto\Address;
use ontio\sdk\Keystore;
use function GuzzleHttp\json_decode;

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

  public function test_import_from_keystore()
  {
    $data = json_decode('{"address":"AG9W6c7nNhaiywcyVPgW9hQKvUYQr5iLvk","key":"+UADcReBcLq0pn/2Grmz+UJsKl3ryop8pgRVHbQVgTBfT0lho06Svh4eQLSmC93j","parameters":{"curve":"P-256"},"label":"11111","scrypt":{"dkLen":64,"n":4096,"p":8,"r":8},"salt":"IfxFV0Fer5LknIyCLP2P2w==","type":"A","algorithm":"ECDSA"}', true);
    $keystore = Keystore::fromJson(json_encode($data));
    $acc = Account::importFromKeystore($keystore, '111111');
    $this->assertEquals($data, $acc->exportKeystore()->jsonSerialize(), 0.0, true);
  }
}
