<?php

namespace ontio\common;

const kSizeofUInt8 = 1;
const kSizeofUInt16 = 2;
const kSizeofUInt32 = 4;
const kSizeofUInt64 = 8;

class ByteArray
{
  /**
   * @var int[]
   */
  public $bytes = [];

  public static function fromHex(string $hex) : self
  {
    return self::fromBinary(hex2bin($hex));
  }

  public static function fromBinary(string $bin) : self
  {
    return new self(array_values(unpack("C*", $bin)));
  }

  public static function fromInt(int $num, $len = 1, bool $littleEndian = false) : self
  {
    $flag;
    switch ($len) {
      case 1:
        $flag = 'C';
        break;
      case 2:
        $flag = $littleEndian ? 'v' : 'n';
        break;
      case 4:
        $flag = $littleEndian ? 'V' : 'N';
        break;
      case 8:
        $flag = $littleEndian ? 'P' : 'J';
        break;
      default:
        throw new \InvalidArgumentException('illegal len');
    }
    $bytes = array_values(unpack("C*", pack($flag, $num)));
    return new self($bytes);
  }

  public static function fromBase64(string $b64) : self
  {
    return self::fromBinary(base64_decode($b64));
  }

  public static function fromBytes(array $bytes) : self
  {
    return new self($bytes);
  }

  public function dup() : self
  {
    return new self(array_values($this->bytes));
  }

  /**
   * @param int[] $bytes
   */
  public function __construct($bytes = [])
  {
    $this->bytes = $bytes;
  }

  public function slice(int $offset, int $length = null) : self
  {
    return new self(array_slice($this->bytes, $offset, $length));
  }

  public function push(int ...$bytes) : self
  {
    array_push($this->bytes, ...$bytes);
    return $this;
  }

  public function pushArray(self $arr) : self
  {
    $this->push(...$arr->bytes);
    return $this;
  }

  public function pushInt(int $num, $len = 1, bool $littleEndian = false) : self
  {
    $buf = self::fromInt($num, $len, $littleEndian);
    $this->pushArray($buf);
    return $this;
  }

  public function reverse() : self
  {
    return new self(array_reverse($this->bytes));
  }

  public function toHex() : string
  {
    return bin2hex($this->toBinary());
  }

  public function toBinary() : string
  {
    return pack("C*", ...$this->bytes);
  }

  public function toBase64() : string
  {
    return base64_encode($this->toBinary());
  }

  public function length() : int
  {
    return count($this->bytes);
  }

  public static function random(int $length) : self
  {
    return self::fromBinary(random_bytes($length));
  }

  public function readUInt8(int $offset) : int
  {
    $sub = array_slice($this->bytes, $offset, 1);
    assert(count($sub) === 1, new \RangeException("invalid offset"));
    return unpack("C", pack("C*", ...$sub))[1];
  }

  public function readInt8(int $offset) : int
  {
    $sub = array_slice($this->bytes, $offset, 1);
    assert(count($sub) === 1, new \RangeException("invalid offset"));
    return unpack("c", pack("C*", ...$sub))[1];
  }

  public function readUInt16(int $offset, int $endian = Endian::Big) : int
  {
    $flag = 'S';
    if ($endian === Endian::Big) {
      $flag = 'n';
    } else if ($endian === Endian::Little) {
      $flag = 'v';
    }
    $sub = array_slice($this->bytes, $offset, 2);
    assert(count($sub) === 2, new \RangeException("invalid offset"));
    return unpack($flag, pack("C*", ...$sub))[1];
  }

  public function readInt16(int $offset, int $endian = Endian::Big) : int
  {
    $flag = 's';
    $sub = array_slice($this->bytes, $offset, 2);
    if ($endian === Endian::Little) {
      $sub = array_reverse($sub);
    }
    assert(count($sub) === 2, new \RangeException("invalid offset"));
    return unpack($flag, pack("C*", ...$sub))[1];
  }

  public function readUInt32(int $offset, int $endian = Endian::Big) : int
  {
    $flag = 'L';
    if ($endian === Endian::Big) {
      $flag = 'N';
    } else if ($endian === Endian::Little) {
      $flag = 'V';
    }
    $sub = array_slice($this->bytes, $offset, 4);
    assert(count($sub) === 4, new \RangeException("invalid offset"));
    return unpack($flag, pack("C*", ...$sub))[1];
  }

  public function readInt32(int $offset, int $endian = Endian::Big) : int
  {
    $flag = 'l';
    $sub = array_slice($this->bytes, $offset, 4);
    if ($endian === Endian::Little) {
      $sub = array_reverse($sub);
    }
    assert(count($sub) === 4, new \RangeException("invalid offset"));
    return unpack($flag, pack("C*", ...$sub))[1];
  }

  public function readUInt64(int $offset, int $endian = Endian::Big) : int
  {
    $flag = 'Q';
    if ($endian === Endian::Big) {
      $flag = 'J';
    } else if ($endian === Endian::Little) {
      $flag = 'P';
    }
    $sub = array_slice($this->bytes, $offset, 8);
    assert(count($sub) === 8, new \RangeException("invalid offset"));
    return unpack($flag, pack("C*", ...$sub))[1];
  }

  public function readInt64(int $offset, int $endian = Endian::Big) : int
  {
    $flag = 'q';
    $sub = array_slice($this->bytes, $offset, 8);
    if ($endian === Endian::Little) {
      $sub = array_reverse($sub);
    }
    assert(count($sub) === 8, new \RangeException("invalid offset"));
    return unpack($flag, pack("C*", ...$sub))[1];
  }
}
