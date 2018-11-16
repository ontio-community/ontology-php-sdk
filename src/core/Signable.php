<?php

namespace ontio\core;

interface Signable
{
  public function getSignContent() : string;

  public function serializeUnsignedData() : string;
}
