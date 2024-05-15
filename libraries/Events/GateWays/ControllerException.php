<?php

namespace packages\sms\Events\GateWays;

class ControllerException extends \Exception
{
    private $controller;

    public function __construct($controller)
    {
        $this->controller = $controller;
    }

    public function getController()
    {
        return $this->controller;
    }
}
