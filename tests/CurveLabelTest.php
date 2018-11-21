<?php

use PHPUnit\Framework\TestCase;

use ontio\crypto\CurveLabel;

final class CurveLabelTest extends TestCase
{
  /**
   * @throws Exception
   */
  public function test_from_value()
  {
    $this->assertEquals(CurveLabel::fromValue(1)->value, 1);
    $this->assertEquals(CurveLabel::fromLabel('sm2p256v1')->label, 'sm2p256v1');
  }
}
