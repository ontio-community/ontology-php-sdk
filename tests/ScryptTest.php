<?php

use PHPUnit\Framework\TestCase;

use ontio\crypto\PrivateKey;
use ontio\Account;
use ontio\ErrorCode;
use ontio\common\ByteArray;
use ontio\crypto\Address;
use ontio\crypto\Scrypt;

final class ScryptTest extends TestCase
{
  public function test_gcm()
  {
    $salt = ByteArray::random(16);
    $prikey = new PrivateKey(ByteArray::fromHex('40b6b5a45bc3ba6bd4f49b0c6b024d5c6851db4cdf1a99c2c7adad9675170b07'));
    $pubkey = $prikey->getPublicKey();
    $address = Address::fromPubKey($pubkey);

    $enc = Scrypt::encryptWithGcm($prikey->key->toBinary(), $address->toBase58(), $salt->toBinary(), '123456');
    $dec = Scrypt::decryptWithGcm(base64_decode($enc), $address->toBase58(), $salt->toBinary(), '123456');

    $this->assertEquals($prikey->key->toBinary(), $dec);
  }

  public function test_scrypt()
  {
    $prikey = new PrivateKey(ByteArray::fromHex('6717c0df45159d5b5ef383521e5d8ed8857a02cdbbfdefeeeb624f9418b0895e'));

    $salt = ByteArray::fromBase64('sJwpxe1zDsBt9hI2iA2zKQ==');
    $address = new Address("AakBoSAJapitE4sMPmW7bs8tfT4YqPeZEU");

    $enc = $prikey->encrypt("11111111", $address, $salt->toBinary());
    $this->assertEquals('dRiHlKa16kKGuWEYWhXUxvHcPlLiJcorAN3ocZ9fQ5HBHBwf47A+MYoMg1nV6UuP', $enc->key->toBase64());

    $enc64 = "dRiHlKa16kKGuWEYWhXUxvHcPlLiJcorAN3ocZ9fQ5HBHBwf47A+MYoMg1nV6UuP";
    $dec = Scrypt::decryptWithGcm(base64_decode($enc64), $address->toBase58(), $salt->toBinary(), "11111111");
    $this->assertEquals($prikey->key->toBinary(), $dec);
  }
}
