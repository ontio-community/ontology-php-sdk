<?php

use PHPUnit\Framework\TestCase;

use ontio\crypto\KeyType;

final class KeyTypeTest extends TestCase
{
  /**
   * @throws Exception
   */
  public function test_static_init()
  {
    $this->assertEquals(KeyType::$Ecdsa->value, 0x12);
    $this->assertEquals(KeyType::$Ecdsa->label, 'ECDSA');
  }
}
