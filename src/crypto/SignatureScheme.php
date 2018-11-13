<?php

namespace ontio\crypto;

use ontio\common\Enumerable;

class SignatureScheme extends Enumerable
{
  /**
   * @var self
   */
  public static $EcdsaWithSha224;

  /**
   * @var self
   */
  public static $EcdsaWithSha256;

  /**
   * @var self
   */
  public static $EcdsaWithSha384;

  /**
   * @var self
   */
  public static $EcdsaWithSha512;

  /**
   * @var self
   */
  public static $EcdsaWithSha3_224;

  /**
   * @var self
   */
  public static $EcdsaWithSha3_256;

  /**
   * @var self
   */
  public static $EcdsaWithSha3_384;

  /**
   * @var self
   */
  public static $EcdsaWithSha3_512;

  /**
   * @var self
   */
  public static $EcdsaWithRipemd160;

  /**
   * @var self
   */
  public static $Sm2WithSm3;

  /**
   * @var self
   */
  public static $EddsaWithSha512;

  /**
   * @var string
   */
  public $labelJws;

  /**
   * @var SignatureSchema[]
   */
  public static $values = [];

  private function __construct(int $value, string $label, string $labelJws)
  {
    parent::__construct($value, $label);
    $this->labelJws = $labelJws;
  }

  public static function _init()
  {
    self::$EcdsaWithSha224 = new self(0, 'SHA224withECDSA', 'ES224');
    self::$EcdsaWithSha256 = new self(1, 'SHA256withECDSA', 'ES256');
    self::$EcdsaWithSha384 = new self(2, 'SHA384withECDSA', 'ES384');
    self::$EcdsaWithSha512 = new self(3, 'SHA512withECDSA', 'ES512');
    self::$EcdsaWithSha3_224 = new self(4, 'SHA3-224withECDSA', 'ES3-224');
    self::$EcdsaWithSha3_256 = new self(5, 'SHA3-256withECDSA', 'ES3-256');
    self::$EcdsaWithSha3_384 = new self(6, 'SHA3-384withECDSA', 'ES3-384');
    self::$EcdsaWithSha3_512 = new self(7, 'SHA3-512withECDSA', 'ES3-512');
    self::$EcdsaWithRipemd160 = new self(8, 'RIPEMD160withECDSA', 'ER160');
    self::$Sm2WithSm3 = new self(9, 'SM3withSM2', 'SM');
    self::$EddsaWithSha512 = new self(10, 'SHA512withEdDSA', 'EDS512');
  }
}

SignatureScheme::_init();
