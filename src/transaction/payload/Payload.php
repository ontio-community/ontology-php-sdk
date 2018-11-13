<?php

namespace ontio\transaction\payload;

use ontio\transaction\ScriptReader;

abstract class Payload
{
  abstract function serialize() : string;
  abstract function deserialize(ScriptReader $ss);
}
