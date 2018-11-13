<?php

use PHPUnit\Framework\TestCase;


final class SignatureSchemeTest extends TestCase
{
  /**
   * @throws Exception
   */
  public function test_static_init()
  {
    $this->assertEquals(\ontio\crypto\SignatureScheme::$EcdsaWithSha224->value, 0);
    $this->assertEquals(\ontio\crypto\SignatureScheme::$EcdsaWithSha224->label, 'SHA224withECDSA');
  }
}
