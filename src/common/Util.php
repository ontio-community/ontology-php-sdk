<?php

namespace ontio\common;

class Util
{

  const JS_ISO = 'Y-m-d\TH:i:s.u\Z';

  const Fixed64Size = 8;

  public static function hash160(string $stuff) : string
  {
    return hash('ripemd160', hash('sha256', $stuff, true));
  }

  /**
   * @param array|\SplFixedArray $src
   * @param int $srcPos
   * @param array|\SplFixedArray $des
   * @param int $desPos
   * @param int $len
   */
  public static function arrayCopy($src, int $srcPos, &$des, int $desPos, int $len)
  {
    while ($len) {
      $des[$desPos++] = $src[$srcPos++];
      $len--;
    }
  }
}
