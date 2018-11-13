<?php

namespace ontio\common;

abstract class Enumerable
{
  /**
   * @var int
   */
  public $value;

  /**
   * @var string
   */
  public $label;

  public function __construct(int $value, string $label)
  {
    $this->value = $value;
    $this->label = $label;

    static::$values[] = $this;
  }

  public static function fromValue(int $value) : self
  {
    $f = \array_filter(static::$values, function ($v) use ($value) {
      return $v->value === $value;
    });

    if (count($f) !== 1)
      throw new \InvalidArgumentException('Enum value not found');

    return array_pop($f);
  }

  public static function fromLabel(string $label) : self
  {
    $f = \array_filter(static::$values, function ($v) use ($label) {
      return $v->label === $label;
    });

    if (count($f) !== 1)
      throw new \InvalidArgumentException('Enum value not found');

    return array_pop($f);
  }

  public abstract static function _init();
}
