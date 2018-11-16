<?php

namespace ontio\core\program;

use ontio\common\ByteArray;
use ontio\core\ErrorCode;
use ontio\crypto\PublicKey;
use ontio\crypto\KeyType;
use Elliptic\EC;
use Elliptic\EC\KeyPair;
use Elliptic\Curve\ShortCurve\Point;
use \GMP;
use ontio\core\scripts\ScriptBuilder;
use ontio\core\scripts\Opcode;

class ProgramBuilder extends ScriptBuilder
{
  public static function programFromPubKey(PublicKey $pk) : self
  {
    $prog = new self();
    $prog->pushPubKey($pk);
    $prog->pushOpcode(Opcode::CHECKSIG);
    return $prog;
  }

  /**
   * @param string[] $sig
   * @return self
   */
  public static function programFromParams($sig) : self
  {
    $prog = new self();
    asort($sig);
    foreach ($sig as $s) {
      $prog->pushBytes(ByteArray::fromHex($s));
    }
    return $prog;
  }

  public static function comparePublicKeys(PublicKey $a, PublicKey $b) : int
  {
    if ($a->algorithm !== $b->algorithm) {
      return $a->algorithm->value - $b->algorithm->value;
    }
    switch ($a->algorithm->value) {
      case KeyType::$Ecdsa->value:
        $ec = new EC($a->parameters->curve->preset);
        /** @var KeyPair $paKey */
        $paKey = $ec->keyFromPublic($a->key->toHex(), 'hex');
        /** @var KeyPair $pbKey */
        $pbKey = $ec->keyFromPublic($b->key->toHex(), 'hex');
        /** @var Point $pa */
        $pa = $paKey->getPublic();
        /** @var Point $pb*/
        $pb = $pbKey->getPublic();
        if ($pa->getX() !== $pb->getX()) {
          return $pa->getX() - $pb->getX();
        }
        return $pa->getY() - $pb->getY();
      case KeyType::$Sm2->value:
        $ec = new EC($a->parameters->curve->preset);
        $paKey = new KeyPair($ec, ['pub' => $a->key->toHex(), 'pubEnc' => 'hex']);
        $pbKey = new KeyPair($ec, ['pub' => $b->key->toHex(), 'pubEnc' => 'hex']);
        /** @var Point $pa */
        $pa = $paKey->getPublic();
        /** @var Point $pb*/
        $pb = $pbKey->getPublic();
        if ($pa->getX() !== $pb->getX()) {
          return $pa->getX() - $pb->getX();
        }
        return $pa->getY() - $pb->getY();
      case KeyType::$Eddsa->value:
        return $a->key->readInt64() - $b->key->readUInt64();
      default:
        return 0;
    }
  }

  public static function programFromMultiPubKey($pubKeys, int $m) : self
  {
    $prog = new self();
    $n = count($pubKeys);
    if (!(1 <= $m && $m <= $n && $n <= 1024)) {
      throw new \InvalidArgumentException('Wrong multi-sig param');
    }

    uasort($pubKeys, array('self', 'comparePublicKeys'));

    $prog->pushNum($m);

    foreach ($pubKeys as $pk) {
      /** @var ByteArray $pk */
      $prog->pushArray($pk->key);
    }

    $prog->pushNum($n);
    $prog->pushOpcode(Opcode::CHECKMULTISIG);
    return $prog;
  }

}
