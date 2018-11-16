<?php

namespace ontio\sdk;

use \Adbar\Dot;

class Constant
{
  public static $DEFAULT_SCRYPT;

  /** @var Dot */
  public static $DEFAULT_ALGORITHM;

  public static $DEFAULT_SM2_ID = '1234567812345678';

  public static $ONT_BIP44_PATH = "m/44'/1024'/0'/0/0";

  public static $ADDR_VERSION = '17';

  public static $TEST_NODE = 'polaris1.ont.io';

  public static $HTTP_JSON_PORT = '20336';

  public static $NATIVE_INVOKE_NAME = 'Ontology.Native.Invoke';

  /** @var Dot */
  public static $TEST_ONT_URL;

  /** @var Dot */
  public static $TOKEN_TYPE;

  public static function _init()
  {
    self::$DEFAULT_SCRYPT = (object)[
      'cost' => 4096,
      'blockSize' => 8,
      'parallel' => 8,
      'size' => 64
    ];

    self::$DEFAULT_ALGORITHM = new Dot([
      'algorithm' => 'ECDSA',
      'parameters' => [
        'curve' => 'P-256'
      ]
    ]);

    $TEST_NODE = self::$TEST_NODE;
    $HTTP_JSON_PORT = self::$HTTP_JSON_PORT;
    self::$TEST_ONT_URL = new Dot([
      'RPC_URL' => "http://{$TEST_NODE}:{$HTTP_JSON_PORT}"
    ]);

    self::$TOKEN_TYPE = new Dot([
      'ONT' => 'ONT',
      'ONG' => 'ONG'
    ]);
  }
}

Constant::_init();
