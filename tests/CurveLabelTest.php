<?php

use PHPUnit\Framework\TestCase;


final class CurveLabelTest extends TestCase
{
  /**
   * @throws Exception
   */
  public function test_from_value()
  {
    $this->assertEquals(\ontio\crypto\CurveLabel::fromValue(1)->value, 1);
    $this->assertEquals(\ontio\crypto\CurveLabel::fromLabel('sm2p256v1')->label, 'sm2p256v1');
  }
}
