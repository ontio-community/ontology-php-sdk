<?php

use PHPUnit\Framework\TestCase;
use ontio\crypto\PrivateKey;
use ontio\crypto\KeyType;
use ontio\common\ByteArray;
use ontio\crypto\SignatureScheme;
use ontio\crypto\KeyParameters;
use ontio\crypto\CurveLabel;
use ontio\crypto\PublicKey;
use ontio\crypto\Address;
use ontio\crypto\ScryptParams;
use ontio\crypto\Signature;

final class KeyTest extends TestCase
{
  /**
   * @throws Exception
   */
  public function test_sm3_hash()
  {
    $key = PrivateKey::random(KeyType::$Sm2);
    $msg = 'test';
    $hash = $key->computeHash(ByteArray::fromBinary($msg), SignatureScheme::$Sm2WithSm3);
    $this->assertEquals($hash, '55e12e91650d2fec56ec74e1d3e4ddbfce2ef3a65890c2a19ecf88a307e76a23');
  }

  public function test_sm2_sign()
  {
    $pk = new PrivateKey(
      ByteArray::fromHex('ab80a7ad086249c01e65c4d9bb6ce18de259dcfc218cd49f2455c539e9112ca3'),
      KeyType::$Sm2,
      new KeyParameters(CurveLabel::$Sm2P256v1)
    );

    $signature = $pk->sign(ByteArray::fromBinary('test'), SignatureScheme::$Sm2WithSm3);

    $pubKey = new PublicKey(
      ByteArray::fromHex('031220580679fda524f575ac48b39b9f74cb0a97993df4fac5798b04c702d07a39'),
      KeyType::$Sm2,
      new KeyParameters(CurveLabel::$Sm2P256v1)
    );
    $this->assertTrue($pubKey->verify(ByteArray::fromBinary('test'), $signature));
  }

  public function test_sm2_sign_keypair()
  {
    $pk = new PrivateKey(
      ByteArray::fromHex('ab80a7ad086249c01e65c4d9bb6ce18de259dcfc218cd49f2455c539e9112ca3'),
      KeyType::$Sm2,
      new KeyParameters(CurveLabel::$Sm2P256v1)
    );

    $signature = $pk->sign(ByteArray::fromBinary('test'), SignatureScheme::$Sm2WithSm3);
    $pubKey = $pk->getPublicKey();
    $this->assertTrue($pubKey->verify(ByteArray::fromBinary('test'), Signature::fromHex($signature->toHex())));
  }

  public function test_sm2_verify_ts_sig()
  {
    $pk = new PrivateKey(
      ByteArray::fromHex('ab80a7ad086249c01e65c4d9bb6ce18de259dcfc218cd49f2455c539e9112ca3'),
      KeyType::$Sm2,
      new KeyParameters(CurveLabel::$Sm2P256v1)
    );
    $sig = Signature::fromHex('09313233343536373831323334353637380061f57a6006df7e8d503dcf8b3261c1309222a44f6b7a6a3184f0fd37e75879d234f38f4e47efd81d616d3ee60440be63d46e1bd75259c2042faf56f415fb7776');
    $pubKey = $pk->getPublicKey();
    $this->assertTrue($pubKey->verify(ByteArray::fromBinary('test'), $sig));
  }

  public function test_to_wif()
  {
    $pk = new PrivateKey(ByteArray::fromHex('e467a2a9c9f56b012c71cf2270df42843a9d7ff181934068b4a62bcdd570e8be'));
    $wif = $pk->toWif();
    $this->assertEquals('L4shZ7B4NFQw2eqKncuUViJdFRq6uk1QUb6HjiuedxN4Q2CaRQKW', $wif);
  }

  public function test_from_wif()
  {
    $wif = 'L4shZ7B4NFQw2eqKncuUViJdFRq6uk1QUb6HjiuedxN4Q2CaRQKW';
    $pk = PrivateKey::fromWif($wif);
    $this->assertEquals('e467a2a9c9f56b012c71cf2270df42843a9d7ff181934068b4a62bcdd570e8be', $pk->key->toHex());
  }

  public function test_java_generated_key()
  {
    $prikey = new PrivateKey(ByteArray::fromHex('176fbdfa6eb71f06d849fdfb9b7a4b879b19d49fa963bb58ce327c417666f5a5'));
    $encPrikey = $prikey->encrypt(
      '123456',
      Address::fromPubKey($prikey->getPublicKey()),
      ByteArray::fromBase64("4vD1aBdikit9C1FNm0zE5Q==")->toBinary(),
      new ScryptParams()
    );
    $this->assertEquals('YRUp1haBykuJvbNCPiTaAU3HunubC47n7bZXveUsAlcNkjo6KF31g+arGq2t2C0t', $encPrikey->key->toBase64());
  }
}
