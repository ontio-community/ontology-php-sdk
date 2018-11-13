<?php

namespace ontio\smartcontract\abi;

class ParameterTypeVal
{
  const ByteArray = 0x00;
  const Boolean = 0x01;
  const Integer = 0x02;
  const interface = 0x40;
  const array = 0x80;
  const Struct = 0x81;
  const Map = 0x82;
}
