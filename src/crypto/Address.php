<?php

namespace ontio\crypto;

use ontio\core\ErrorCode;
use ontio\core\program\ProgramBuilder;
use ontio\common\Util;
use BitWasp\Bitcoin\Base58;
use BitWasp\Buffertools\Buffer;
use ontio\sdk\Constant;
use ontio\common\ByteArray;

class Address
{

  /**
   * hex encoded string
   *
   * @var string
   */
  public $value;

  public function __construct($value)
  {
    if (is_string($value)) {
      $len = strlen($value);
      if ($len !== 40 && $len !== 34) {
        throw new \InvalidArgumentException(ErrorCode::INVALID_PARAMS);
      }
      $this->value = $value;
    } else if ($value instanceof ByteArray) {
      $this->value = $value->toHex();
    } else {
      throw new \InvalidArgumentException(ErrorCode::INVALID_PARAMS);
    }
  }

  public function toBase58() : string
  {
    if (strlen($this->value) === 34) return $this->value;
    return self::hexToBase58($this->value);
  }

  public static function hexToBase58(string $hex) : string
  {
    $data = Constant::$ADDR_VERSION . $hex;
    $hash = hash('sha256', hex2bin($data));
    $hash2 = hash('sha256', hex2bin($hash));
    $checksum = substr($hash2, 0, 8);
    $data = $data . $checksum;
    return Base58::encode(Buffer::hex($data));
  }

  public static function base58ToHex(string $b58) : string
  {
    $data = Base58::decode($b58)->getHex();
    $hex = substr($data, 2, 40);
    if ($b58 !== self::hexToBase58($hex)) {
      throw new \InvalidArgumentException('[addressToU160] decode encoded verify failed');
    }
    return $hex;
  }

  public static function fromPubKey(PublicKey $pubKey) : self
  {
    $prog = ProgramBuilder::programFromPubKey($pubKey);
    $hash = Util::hash160($prog->toBinary());
    return new self($hash);
  }

  public function serialize() : string
  {
    if (strlen($this->value) === 40) return $this->value;
    return self::base58ToHex($this->value);
  }

  public function toHex() : string
  {
    $val = $this->value;
    if (strlen($this->value) !== 40) {
      $val = self::base58ToHex($this->value);
    }
    return ByteArray::fromHex($val)->reverse()->toHex();
  }

  /**
   * Creates new address from VM code
   *
   * @param string $code hex encoded code string
   * @return self
   */
  public static function fromVmCode(string $code) : self
  {
    $hash = Util::hash160(hex2bin($code));
    return new self($hash);
  }

  public static function generateOntid(PublicKey $pubkey) : string
  {
    $addr = self::fromPubKey($pubkey);
    $id = 'did:ont:' . $addr->toBase58();
    return $id;
  }

  public static function fromOntId(string $id) : self
  {
    return new self(substr($id, 8));
  }
}
