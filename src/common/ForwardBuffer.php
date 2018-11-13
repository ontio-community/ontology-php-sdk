<?php

namespace ontio\common;

class ForwardBuffer
{
  /**
   * @var ByteArray
   */
  protected $buf;

  /**
   * @var int
   */
  protected $ofst;

  public function __construct(ByteArray $buf, int $offset = 0)
  {
    $this->buf = $buf;
    $this->ofst = $offset;
  }

  public function buffer() : ByteArray
  {
    return $this->buf;
  }

  public function offset() : int
  {
    return $this->ofst;
  }

  public function readUInt8() : int
  {
    $n = $this->buf->readUInt8($this->ofst);
    $this->ofst += kSizeofUInt8;
    return $n;
  }

  public function readUInt16BE() : int
  {
    $n = $this->buf->readUInt16($this->ofst);
    $this->ofst += kSizeofUInt16;
    return $n;
  }

  public function readUInt16LE() : int
  {
    $n = $this->buf->readUInt16($this->ofst, Endian::Little);
    $this->ofst += kSizeofUInt16;
    return $n;
  }

  public function readUInt32BE() : int
  {
    $n = $this->buf->readUInt32($this->ofst);
    $this->ofst += kSizeofUInt32;
    return $n;
  }

  public function readUInt32LE() : int
  {
    $n = $this->buf->readUInt32($this->ofst, Endian::Little);
    $this->ofst += kSizeofUInt32;
    return $n;
  }

  public function readInt8() : int
  {
    $n = $this->buf->readInt8($this->ofst);
    $this->ofst += kSizeofUInt8;
    return $n;
  }

  public function readInt16BE() : int
  {
    $n = $this->buf->readInt16($this->ofst);
    $this->ofst += kSizeofUInt16;
    return $n;
  }

  public function readInt32BE() : int
  {
    $n = $this->buf->readInt32($this->ofst);
    $this->ofst += kSizeofUInt32;
    return $n;
  }

  public function readInt32LE() : int
  {
    $n = $this->buf->readInt32($this->ofst, Endian::Little);
    $this->ofst += kSizeofUInt32;
    return $n;
  }

  public function readInt64BE() : int
  {
    $n = $this->buf->readInt64($this->ofst);
    $this->ofst += kSizeofUInt64;
    return $n;
  }

  public function readUInt64BE() : int
  {
    $n = $this->buf->readUInt64($this->ofst);
    $this->ofst += kSizeofUInt64;
    return $n;
  }

  public function readUInt64LE() : int
  {
    $n = $this->buf->readUInt64($this->ofst, Endian::Little);
    $this->ofst += kSizeofUInt64;
    return $n;
  }

  public function forward(int $cnt) : ByteArray
  {
    $sub = $this->buf->slice($this->ofst, $cnt);
    $this->ofst += count($sub->bytes);
    return $sub;
  }

  public function forwardUntil($fn) : ByteArray
  {
    $ret = new ByteArray([]);
    for ($i = $this->ofst, $len = $this->buf->length(); $i < $len; ++$i, ++$this->ofst) {
      $byte = $this->buf->bytes[$this->ofst];
      $stop = $fn($byte);
      if ($stop) break;
      $ret->push($this->buf->bytes[$this->ofst]);
    }
    return $ret;
  }

  public function advance(int $cnt)
  {
    $this->ofst += $cnt;
  }

  public function branch(int $offset = 0) : self
  {
    return new ForwardBuffer($this->buf, $offset);
  }

  public function isEnd() : bool
  {
    assert($this->ofst <= $this->buf->length());
    return $this->ofst === $this->buf->length();
  }
}
