<?php

namespace ontio\core\payload;

use ontio\core\scripts\ScriptReader;

abstract class Payload
{
  abstract function serialize() : string;
  abstract function deserialize(ScriptReader $ss);
}
