<?php

use PHPUnit\Framework\TestCase;

use ontio\crypto\SignatureScheme;

final class SignatureSchemeTest extends TestCase
{
  /**
   * @throws Exception
   */
  public function test_static_init()
  {
    $this->assertEquals(SignatureScheme::$EcdsaWithSha224->value, 0);
    $this->assertEquals(SignatureScheme::$EcdsaWithSha224->label, 'SHA224withECDSA');
  }
}
