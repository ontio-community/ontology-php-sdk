<?php

namespace ontio\crypto;

use BitWasp\Bitcoin\Bitcoin;
use BitWasp\Bitcoin\Crypto\EcAdapter\Adapter\EcAdapterInterface;
use BitWasp\Bitcoin\Crypto\EcAdapter\EcSerializer;
use BitWasp\Bitcoin\Crypto\EcAdapter\Serializer\Key\PublicKeySerializerInterface;
use BitWasp\Bitcoin\Crypto\Hash;
use BitWasp\Bitcoin\Key\Deterministic\HierarchicalKey;
use BitWasp\Bitcoin\Key\Factory\PrivateKeyFactory;
use BitWasp\Bitcoin\Key\KeyToScript\Factory\P2pkhScriptDataFactory;
use BitWasp\Bitcoin\Key\KeyToScript\ScriptDataFactory;
use BitWasp\Bitcoin\Serializer\Key\HierarchicalKey\Base58ExtendedKeySerializer;
use BitWasp\Bitcoin\Serializer\Key\HierarchicalKey\ExtendedKeySerializer;
use BitWasp\Buffertools\Buffer;
use BitWasp\Buffertools\BufferInterface;

class HDKeyFactory
{
  /**
   * @var EcAdapterInterface
   */
  private $adapter;

  /**
   * @var Base58ExtendedKeySerializer
   */
  private $serializer;

  /**
   * @var PrivateKeyFactory
   */
  private $privFactory;

  /**
   * HierarchicalKeyFactory constructor.
   * @param EcAdapterInterface|null $ecAdapter
   * @param Base58ExtendedKeySerializer|null $serializer
   * @throws \Exception
   */
  public function __construct(EcAdapterInterface $ecAdapter = null, Base58ExtendedKeySerializer $serializer = null)
  {
    $this->adapter = $ecAdapter ? : Bitcoin::getEcAdapter();
    $this->privFactory = PrivateKeyFactory::compressed($this->adapter);
    $this->serializer = $serializer ? : new Base58ExtendedKeySerializer(
      new ExtendedKeySerializer($this->adapter)
    );
  }

  /**
   * @param BufferInterface $entropy
   * @param ScriptDataFactory|null $scriptFactory
   * @return HierarchicalKey
   * @throws \Exception
   */
  public function fromEntropy(BufferInterface $entropy, ScriptDataFactory $scriptFactory = null) : HierarchicalKey
  {
    $seed = Hash::hmac('sha512', $entropy, new Buffer('Nist256p1 seed'));
    $privSecret = $seed->slice(0, 32);
    $chainCode = $seed->slice(32, 32);
    $scriptFactory = $scriptFactory ? : new P2pkhScriptDataFactory(EcSerializer::getSerializer(PublicKeySerializerInterface::class, true, $this->adapter));
    return new HierarchicalKey($this->adapter, $scriptFactory, 0, 0, 0, $chainCode, $this->privFactory->fromBuffer($privSecret));
  }
}
