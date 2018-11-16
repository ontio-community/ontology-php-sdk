<?php

use PHPUnit\Framework\TestCase;
use ontio\crypto\PrivateKey;
use ontio\sdk\Identity;
use Datto\JsonRpc\Exceptions\Exception;
use ontio\core\ErrorCode;

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
    $id = Identity::importIdentity('mickey', self::$encPrikey, '123456', self::$addr, self::$id->controls[0]->salt);
    $this->assertEquals('mickey', $id->label);
  }

  public function test_import_incorrect_pwd()
  {
    try {
      $id = Identity::importIdentity('mickey', self::$encPrikey, '1234567', self::$addr, self::$id->controls[0]->salt);
    } catch (\Exception $e) {
      $this->assertEquals(ErrorCode::DECRYPT_ERROR, (int)$e->getMessage());
    }
  }
}
