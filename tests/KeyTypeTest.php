<?php

use PHPUnit\Framework\TestCase;


final class KeyTypeTest extends TestCase
{
  /**
   * @throws Exception
   */
  public function test_static_init()
  {
    $this->assertEquals(\ontio\crypto\KeyType::$Ecdsa->value, 0x12);
    $this->assertEquals(\ontio\crypto\KeyType::$Ecdsa->label, 'ECDSA');
  }
}
