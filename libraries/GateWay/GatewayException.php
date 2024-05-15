<?php

namespace packages\sms\GateWay;

class GatewayException extends \Exception
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function getData($data)
    {
        return $this->data;
    }
}
