<?php

use PHPUnit\Framework\TestCase;
use ontio\crypto\PrivateKey;
use ontio\sdk\Identity;
use Datto\JsonRpc\Exceptions\Exception;
use ontio\core\ErrorCode;
use ontio\sdk\Keystore;

final class IdentityTest extends TestCase
{
  /** @var PrivateKey */
  public static $prikey;

  /** @var Identity */
  public static $id;

  /** @var PrivateKey */
  public static $encPrikey;

  /** @var Address */
  public static $addr;

  public static function setUpBeforeClass()
  {
    self::$prikey = PrivateKey::random();
  }

  public function test_create()
  {
    self::$id = Identity::create(self::$prikey, '123456', 'mickey');

    self::$addr = self::$id->controls[0]->address;
    self::$encPrikey = self::$id->controls[0]->encryptedKey;

    $idStr = self::$id->jsonSerialize();
    $tmp = Identity::fromJson(json_encode($idStr));
    $this->assertTrue(!empty($tmp));

    $pri = self::$id->exportPrivateKey('123456');
    $this->assertEquals(self::$prikey->key->toHex(), $pri->key->toHex());
  }

  public function test_import_correct_pwd()
  {
    $id = Identity::import('mickey', self::$encPrikey, '123456', self::$addr, self::$id->controls[0]->salt);
    $this->assertEquals('mickey', $id->label);
  }

  public function test_import_incorrect_pwd()
  {
    try {
      $id = Identity::import('mickey', self::$encPrikey, '1234567', self::$addr, self::$id->controls[0]->salt);
    } catch (\Exception $e) {
      $this->assertEquals(ErrorCode::DECRYPT_ERROR, (int)$e->getMessage());
    }
  }

  public function test_import_keystore()
  {
    $data = json_decode('{"address":"AG9W6c7nNhaiywcyVPgW9hQKvUYQr5iLvk","key":"+UADcReBcLq0pn/2Grmz+UJsKl3ryop8pgRVHbQVgTBfT0lho06Svh4eQLSmC93j","parameters":{"curve":"P-256"},"label":"11111","scrypt":{"dkLen":64,"n":4096,"p":8,"r":8},"salt":"IfxFV0Fer5LknIyCLP2P2w==","type":"I","algorithm":"ECDSA"}', true);
    $keystore = Keystore::fromJson(json_encode($data));
    $id = Identity::importFromKeystore($keystore, '111111');
    $this->assertEquals($data, $id->exportKeystore()->jsonSerialize(), 0.0, true);
  }
}
