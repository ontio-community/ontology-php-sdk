<?php

namespace ontio\core\transaction;

use ontio\crypto\Address;
use ontio\common\ByteArray;
use ontio\core\payload\Payload;
use ontio\common\Fixed64;
use ontio\core\payload\InvokeCode;
use ontio\core\payload\DeployCode;
use ontio\core\Signable;
use ontio\core\scripts\ScriptBuilder;
use ontio\core\scripts\ScriptReader;

class Transaction implements Signable
{
  /** @var TxType */
  public $type = TxType::Invoke;

  /** @var int */
  public $version = 0x00;

  /** @var Payload */
  public $payload;

  /** @var string */
  public $nonce;

  /** @var Fixed64 */
  public $gasPrice;

  /** @var Fixed64 */
  public $gasLimit;

  /** @var Address */
  public $payer;

  /** @var TxSignature[] */
  public $sigs = [];

  public function __construct()
  {
    $this->nonce = ByteArray::random(4)->toHex();
    $this->payer = new Address('0000000000000000000000000000000000000000');
    $this->gasPrice = new Fixed64();
    $this->gasLimit = new Fixed64();
  }

  public function serialize() : string
  {
    $unsigned = $this->serializeUnsignedData();
    $signed = $this->serializeSignedData();
    return $unsigned . $signed;
  }

  public function serializeUnsignedData() : string
  {
    $ret = [];
    $ret[] = ByteArray::fromInt($this->version)->toHex();
    $ret[] = ByteArray::fromInt($this->type)->toHex();

    $ret[] = $this->nonce;
    $ret[] = $this->gasPrice->serialize();
    $ret[] = $this->gasLimit->serialize();
    $ret[] = $this->payer->serialize();
    $ret[] = $this->payload->serialize();

    $ret[] = '00';
    return implode('', $ret);
  }

  public function serializeSignedData() : string
  {
    $builder = new ScriptBuilder();
    $ret = $builder->pushVarInt(count($this->sigs))->toHex();
    foreach ($this->sigs as $sig) {
      $ret = $ret . $sig->serialize();
    }
    return $ret;
  }

  public function getSignContent() : string
  {
    $data = $this->serializeUnsignedData();
    $data = ByteArray::fromHex($data)->toBinary();
    return hash('sha256', hash('sha256', $data, true));
  }

  public static function deserialize(ScriptReader $r) : self
  {
    $tx = new self();
    $tx->version = $r->readUInt8();
    $tx->type = $r->readUInt8();
    $tx->nonce = $r->readUInt32LE();
    $tx->gasPrice = Fixed64::deserialize($r);
    $tx->gasLimit = Fixed64::deserialize($r);
    $tx->payer = new Address($r->forward(20)->toHex());
    $payload;
    switch ($tx->type) {
      case TxType::Invoke:
        $payload = new InvokeCode();
        break;
      case TxType::Deploy:
        $payload = new DeployCode();
        break;
      default:
        $payload = new InvokeCode();
    }
    $payload->deserialize($r);

    $tx->payload = $payload;
    $tx->sigs = [];

    $r->readUInt8();
    $sigLen = $r->readVarInt();
    for ($i = 0; $i < $sigLen; $i++) {
      $buf = $r->buffer()->slice($r->offset());
      $tx->sigs[] = TxSignature::deserialize(new ProgramReader($buf));
    }

    return $tx;
  }
}
