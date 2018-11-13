<?php

namespace ontio\crypto;

class KeyParameters implements \JsonSerializable
{
  /**
   * @var CurveLabel
   */
  public $curve;

  public function __construct(CurveLabel $curve)
  {
    $this->curve = $curve;
  }

  public static function fromCurve(string $curve) : self
  {
    return new self(CurveLabel::fromLabel($curve));
  }

  public static function fromJsonObj($obj) : self
  {
    return self::fromCurve($obj->curve);
  }

  public function jsonSerialize()
  {
    return [
      'curve' => $this->curve->label
    ];
  }
}
