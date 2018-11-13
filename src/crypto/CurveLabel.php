<?php

namespace ontio\crypto;

use ontio\common\Enumerable;

class CurveLabel extends Enumerable
{
  /**
   * @var self
   */
  public static $Secp224r1;

  /**
   * @var self
   */
  public static $Secp256r1;

  /**
   * @var self
   */
  public static $Secp384r1;

  /**
   * @var self
   */
  public static $Secp521r1;

  /**
   * @var self
   */
  public static $Sm2P256v1;

  /**
   * @var self
   */
  public static $Ed25519;

  /**
   * @var string
   */
  public $preset;

  /**
   * @var CurveLabel[]
   */
  public static $values = [];

  public function __construct(int $value, string $label, string $preset)
  {
    parent::__construct($value, $label);
    $this->preset = $preset;
  }

  public static function _init()
  {
    self::$Secp224r1 = new self(1, 'P-224', 'p224');
    self::$Secp256r1 = new self(2, 'P-256', 'p256');
    self::$Secp384r1 = new self(3, 'P-384', 'p384');
    self::$Secp521r1 = new self(4, 'P-521', 'p521');
    self::$Sm2P256v1 = new self(20, 'sm2p256v1', 'sm2p256v1');
    self::$Ed25519 = new self(25, 'ed25519', 'ed25519');
  }
}

CurveLabel::_init();
