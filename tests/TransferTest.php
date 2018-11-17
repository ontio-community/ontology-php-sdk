<?php

use PHPUnit\Framework\TestCase;

use ontio\crypto\Address;
use ontio\core\transaction\TransactionBuilder;
use ontio\smartcontract\nativevm\OntAssetTxBuilder;
use ontio\common\ByteArray;
use ontio\core\scripts\ScriptReader;
use ontio\sdk\Wallet;
use ontio\crypto\PrivateKey;
use ontio\network\JsonRpc;
use ontio\crypto\ScryptParams;
use ontio\crypto\KeyType;
use ontio\crypto\KeyParameters;
use ontio\crypto\CurveLabel;
use ontio\crypto\SignatureScheme;

final class TransferTest extends TestCase
{
  /** @var Wallet */
  public static $wallet;

  /** @var PrivateKey */
  public static $adminPrivateKey;

  /** @var Address */
  public static $adminAddress;

  /** @var string */
  public static $gasLimit;

  /** @var string */
  public static $gasPrice;

  /** @var JsonRpc */
  public static $rpc;

  public static $sm2Account;

  public static function setUpBeforeClass()
  {
    // pwd: 123456
    $walletData = '{"name":"MyWallet","version":"1.1","scrypt":{"p":8,"n":16384,"r":8,"dkLen":64},"accounts":[{"address":"ASSxYHNSsh4FdF2iNvHdh3Np2sgWU21hfp","enc-alg":"aes-256-gcm","key":"t2Kk2jNL4BAoXlYn309DKfxogxJRNvsJ8+GG4kiMB+UvWGXEilYRzfIYeNZbfVbu","algorithm":"ECDSA","salt":"CdRa1hTiOaVESNfJJmcMNw==","parameters":{"curve":"P-256"},"label":"","publicKey":"0344ea636caaebf23c7cec2219a75bd6260f891413467922975447ba57f3c824c6","signatureScheme":"SHA256withECDSA","isDefault":true,"lock":false},{"address":"AL9PtS6F8nue5MwxhzXCKaTpRb3yhtsix5","enc-alg":"aes-256-gcm","key":"vwIgX3qJO+1XikdPAfjAu/clsgS2l2xkEWsRR9XZQ8OyFViX+r/6Yq+cV0wnKQUM","algorithm":"SM2","salt":"xzvrFkHAgsEeX64V+4mpLw==","parameters":{"curve":"sm2p256v1"},"label":"","publicKey":"131403a9b89a0443ded240c3dee97221353d000d0dc905b7c085f4ef558b234a75e122","signatureScheme":"SM3withSM2","isDefault":false,"lock":false}]}';
    self::$wallet = Wallet::fromJson($walletData);
    self::$adminPrivateKey = self::$wallet->accounts[0]->exportPrivateKey('123456', self::$wallet->scrypt);
    self::$adminAddress = self::$wallet->accounts[0]->address;
    self::$gasLimit = '20000';
    self::$gasPrice = '0';

    self::$rpc = new JsonRpc('http://127.0.0.1:20336');
  }

  public function test_transfer_ont()
  {
    $from = self::$adminAddress;
    $to = new Address('AL9PtS6F8nue5MwxhzXCKaTpRb3yhtsix5');

    $ontBuilder = new OntAssetTxBuilder();
    $tx = $ontBuilder->makeTransferTx('ONT', self::$adminAddress, $to, 170, self::$gasPrice, self::$gasLimit);
    $txBuilder = new TransactionBuilder();
    $txBuilder->signTransaction($tx, self::$adminPrivateKey);

    $res = self::$rpc->sendRawTransaction($tx->serialize(), false);
    $this->assertEquals('SUCCESS', $res->desc);
  }

  public function test_transfer_ong()
  {
    $from = self::$adminAddress;
    $to = new Address('AL9PtS6F8nue5MwxhzXCKaTpRb3yhtsix5');

    $ongBuilder = new OntAssetTxBuilder();
    $tx = $ongBuilder->makeTransferTx('ONG', $from, $to, 170, self::$gasPrice, self::$gasLimit);

    $txBuilder = new TransactionBuilder();
    $txBuilder->signTransaction($tx, self::$adminPrivateKey);

    $res = self::$rpc->sendRawTransaction($tx->serialize(), false);
    $this->assertEquals('SUCCESS', $res->desc);
  }

  public function test_get_balance()
  {
    $to = new Address('AL9PtS6F8nue5MwxhzXCKaTpRb3yhtsix5');
    $res = self::$rpc->getBalance($to);
    $this->assertEquals('SUCCESS', $res->desc);
  }

  public function test_transfer_with_sm2_account()
  {
    $sm2Account = self::$wallet->accounts[1];
    $prikey = $sm2Account->exportPrivateKey('123456', self::$wallet->scrypt);
    $pubkey = $prikey->getPublicKey();
    $from = Address::fromPubKey($pubkey);
    $to = new Address('ASSxYHNSsh4FdF2iNvHdh3Np2sgWU21hfp');

    $ontBuilder = new OntAssetTxBuilder();
    $tx = $ontBuilder->makeTransferTx(
      'ONT',
      $from,
      $to,
      100,
      self::$gasPrice,
      self::$gasLimit
    );

    $txBuilder = new TransactionBuilder();
    $txBuilder->signTransaction($tx, $prikey, SignatureScheme::$Sm2WithSm3);

    $res = self::$rpc->sendRawTransaction($tx->serialize(), false);
    $this->assertEquals('SUCCESS', $res->desc);
  }
}
