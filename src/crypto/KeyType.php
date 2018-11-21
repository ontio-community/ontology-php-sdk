<?php

namespace ontio\crypto;

use ontio\common\Enumerable;

class KeyType extends Enumerable
{
  /**
   * @var self
   */
  public static $Ecdsa;

  /**
   * @var self
   */
  public static $Sm2;

  /**
   * @var self
   */
  public static $Eddsa;

  /**
   * @var SignatureScheme
   */
  public $defaultScheme;

  /**
   * @var static[]
   */
  public static $values = [];

  private function __construct(int $value, string $label, SignatureScheme $defaultScheme = null)
  {
    parent::__construct($value, $label);
    $this->defaultScheme = $defaultScheme;
  }

  public static function _init()
  {
    self::$Ecdsa = new self(0x12, 'ECDSA', SignatureScheme::$EcdsaWithSha256);
    self::$Sm2 = new self(0x13, 'SM2', SignatureScheme::$Sm2WithSm3);
    self::$Eddsa = new self(0x14, 'EDDSA', SignatureScheme::$EddsaWithSha512);
  }
}

KeyType::_init();
