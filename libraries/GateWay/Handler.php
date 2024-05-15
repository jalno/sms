<?php

namespace packages\sms\GateWay;

use packages\sms\GateWay;
use packages\sms\Sent;

abstract class Handler
{
    abstract public function __construct(GateWay $gateway);

    abstract public function send(Sent $sms);
}
