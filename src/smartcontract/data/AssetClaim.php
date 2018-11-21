<?php

namespace ontio\smartcontract\data;

class AssetClaim
{
  public static $abi;
}

AssetClaim::$abi = array(
  'hash' => '36bb5c053b6b839c8f6b923fe852f91239b9fccc',
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
      'name' => 'Commit',
      'parameters' =>
        array(
        0 =>
          array(
          'name' => 'claimId',
          'type' => 'ByteArray',
        ),
        1 =>
          array(
          'name' => 'commiterId',
          'type' => 'ByteArray',
        ),
        2 =>
          array(
          'name' => 'ownerId',
          'type' => 'ByteArray',
        ),
      ),
      'returntype' => 'Boolean',
    ),
    2 =>
      array(
      'name' => 'Revoke',
      'parameters' =>
        array(
        0 =>
          array(
          'name' => 'claimId',
          'type' => 'ByteArray',
        ),
        1 =>
          array(
          'name' => 'ontId',
          'type' => 'ByteArray',
        ),
      ),
      'returntype' => 'Boolean',
    ),
    3 =>
      array(
      'name' => 'GetStatus',
      'parameters' =>
        array(
        0 =>
          array(
          'name' => 'claimId',
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
      'name' => 'ErrorMsg',
      'parameters' =>
        array(
        0 =>
          array(
          'name' => 'arg1',
          'type' => 'ByteArray',
        ),
        1 =>
          array(
          'name' => 'arg2',
          'type' => 'String',
        ),
      ),
      'returntype' => 'Void',
    ),
    1 =>
      array(
      'name' => 'Push',
      'parameters' =>
        array(
        0 =>
          array(
          'name' => 'arg1',
          'type' => 'ByteArray',
        ),
        1 =>
          array(
          'name' => 'arg2',
          'type' => 'String',
        ),
        2 =>
          array(
          'name' => 'arg3',
          'type' => 'ByteArray',
        ),
      ),
      'returntype' => 'Void',
    ),
  ),
);
