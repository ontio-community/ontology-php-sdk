<?php

namespace ontio\transaction;

interface Signable
{
  public function getSignContent() : string;

  public function serializeUnsignedData() : string;
}
