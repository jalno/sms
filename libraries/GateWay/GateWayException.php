<?php

namespace packages\sms\GateWay;

class GateWayException extends \Exception
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
