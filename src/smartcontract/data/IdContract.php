<?php

namespace ontio\smartcontract\data;

class IdContract
{
  public static $id;
}

IdContract::$id = array(
  'hash' => 'ff00000000000000000000000000000000000003',
  'entrypoint' => 'Main',
  'functions' =>
    array(
    0 =>
      array(
      'name' => 'Main',
      'parameters' =>
        array(
        0 =>
          array(
          'name' => 'operation',
          'type' => 'String',
        ),
        1 =>
          array(
          'name' => 'args',
          'type' => 'Array',
        ),
      ),
      'returntype' => 'Any',
    ),
    1 =>
      array(
      'name' => 'regIDWithPublicKey',
      'parameters' =>
        array(
        0 =>
          array(
          'name' => 'ontId',
          'type' => 'ByteArray',
        ),
        1 =>
          array(
          'name' => 'publicKey',
          'type' => 'ByteArray',
        ),
      ),
      'returntype' => 'Boolean',
    ),
    2 =>
      array(
      'name' => 'regIDWithAttributes',
      'parameters' =>
        array(
        0 =>
          array(
          'name' => 'ontId',
          'type' => 'ByteArray',
        ),
        1 =>
          array(
          'name' => 'publicKey',
          'type' => 'ByteArray',
        ),
        2 =>
          array(
          'name' => 'tuples',
          'type' => 'ByteArray',
        ),
      ),
      'returntype' => 'Boolean',
    ),
    3 =>
      array(
      'name' => 'addKey',
      'parameters' =>
        array(
        0 =>
          array(
          'name' => 'ontId',
          'type' => 'ByteArray',
        ),
        1 =>
          array(
          'name' => 'newPublicKey',
          'type' => 'ByteArray',
        ),
        2 =>
          array(
          'name' => 'sender',
          'type' => 'ByteArray',
        ),
      ),
      'returntype' => 'Boolean',
    ),
    4 =>
      array(
      'name' => 'removeKey',
      'parameters' =>
        array(
        0 =>
          array(
          'name' => 'ontId',
          'type' => 'ByteArray',
        ),
        1 =>
          array(
          'name' => 'oldPublicKey',
          'type' => 'ByteArray',
        ),
        2 =>
          array(
          'name' => 'sender',
          'type' => 'ByteArray',
        ),
      ),
      'returntype' => 'Boolean',
    ),
    5 =>
      array(
      'name' => 'addRecovery',
      'parameters' =>
        array(
        0 =>
          array(
          'name' => 'ontId',
          'type' => 'ByteArray',
        ),
        1 =>
          array(
          'name' => 'recovery',
          'type' => 'ByteArray',
        ),
        2 =>
          array(
          'name' => 'publicKey',
          'type' => 'ByteArray',
        ),
      ),
      'returntype' => 'Boolean',
    ),
    6 =>
      array(
      'name' => 'changeRecovery',
      'parameters' =>
        array(
        0 =>
          array(
          'name' => 'ontId',
          'type' => 'ByteArray',
        ),
        1 =>
          array(
          'name' => 'newRecovery',
          'type' => 'ByteArray',
        ),
        2 =>
          array(
          'name' => 'recovery',
          'type' => 'ByteArray',
        ),
      ),
      'returntype' => 'Boolean',
    ),
    7 =>
      array(
      'name' => 'addAttributes',
      'parameters' =>
        array(
        0 =>
          array(
          'name' => 'ontId',
          'type' => 'ByteArray',
        ),
        1 =>
          array(
          'name' => 'attributes',
          'type' => 'ByteArray',
        ),
        2 =>
          array(
          'name' => 'publicKey',
          'type' => 'ByteArray',
        ),
      ),
      'returntype' => 'Boolean',
    ),
    8 =>
      array(
      'name' => 'removeAttribute',
      'parameters' =>
        array(
        0 =>
          array(
          'name' => 'ontId',
          'type' => 'ByteArray',
        ),
        1 =>
          array(
          'name' => 'path',
          'type' => 'ByteArray',
        ),
        2 =>
          array(
          'name' => 'publicKey',
          'type' => 'ByteArray',
        ),
      ),
      'returntype' => 'Boolean',
    ),
    9 =>
      array(
      'name' => 'getPublicKeys',
      'parameters' =>
        array(
        0 =>
          array(
          'name' => 'ontId',
          'type' => 'ByteArray',
        ),
      ),
      'returntype' => 'ByteArray',
    ),
    10 =>
      array(
      'name' => 'getAttributes',
      'parameters' =>
        array(
        0 =>
          array(
          'name' => 'ontId',
          'type' => 'ByteArray',
        ),
      ),
      'returntype' => 'ByteArray',
    ),
    11 =>
      array(
      'name' => 'GetPublicKeyId',
      'parameters' =>
        array(
        0 =>
          array(
          'name' => 'ontId',
          'type' => 'ByteArray',
        ),
        1 =>
          array(
          'name' => 'publicKey',
          'type' => 'ByteArray',
        ),
      ),
      'returntype' => 'ByteArray',
    ),
    12 =>
      array(
      'name' => 'getKeyState',
      'parameters' =>
        array(
        0 =>
          array(
          'name' => 'ontId',
          'type' => 'ByteArray',
        ),
        1 =>
          array(
          'name' => 'pkId',
          'type' => 'Int',
        ),
      ),
      'returntype' => 'ByteArray',
    ),
    13 =>
      array(
      'name' => 'GetRecovery',
      'parameters' =>
        array(
        0 =>
          array(
          'name' => 'ontId',
          'type' => 'ByteArray',
        ),
      ),
      'returntype' => 'ByteArray',
    ),
    14 =>
      array(
      'name' => 'getDDO',
      'parameters' =>
        array(
        0 =>
          array(
          'name' => 'id',
          'type' => 'ByteArray',
        ),
      ),
      'returntype' => 'ByteArray',
    ),
  ),
  'events' =>
    array(
    0 =>
      array(
      'name' => 'Register',
      'parameters' =>
        array(
        0 =>
          array(
          'name' => 'op',
          'type' => 'String',
        ),
        1 =>
          array(
          'name' => 'ontId',
          'type' => 'ByteArray',
        ),
      ),
      'returntype' => 'Void',
    ),
    1 =>
      array(
      'name' => 'PublicKey',
      'parameters' =>
        array(
        0 =>
          array(
          'name' => 'op',
          'type' => 'String',
        ),
        1 =>
          array(
          'name' => 'ontId',
          'type' => 'ByteArray',
        ),
        2 =>
          array(
          'name' => 'publicKey',
          'type' => 'ByteArray',
        ),
      ),
      'returntype' => 'Void',
    ),
    2 =>
      array(
      'name' => 'Attribute',
      'parameters' =>
        array(
        0 =>
          array(
          'name' => 'op',
          'type' => 'String',
        ),
        1 =>
          array(
          'name' => 'ontId',
          'type' => 'ByteArray',
        ),
        2 =>
          array(
          'name' => 'attrName',
          'type' => 'ByteArray',
        ),
      ),
      'returntype' => 'Void',
    ),
    3 =>
      array(
      'name' => 'Debug',
      'parameters' =>
        array(
        0 =>
          array(
          'name' => 'func',
          'type' => 'String',
        ),
        1 =>
          array(
          'name' => 'info',
          'type' => 'ByteArray',
        ),
      ),
      'returntype' => 'Void',
    ),
    4 =>
      array(
      'name' => 'Debug',
      'parameters' =>
        array(
        0 =>
          array(
          'name' => 'func',
          'type' => 'String',
        ),
        1 =>
          array(
          'name' => 'trace',
          'type' => 'Integer',
        ),
      ),
      'returntype' => 'Void',
    ),
  ),
);
