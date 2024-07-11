<?php

namespace packages\sms\Events;

use packages\base\Event;
use packages\sms\Events\GateWays\GateWay;

class GateWays extends Event
{
    private $gateways = [];

    public function addGateway(GateWay $gateway)
    {
        $this->gateways[$gateway->getName()] = $gateway;
    }

    public function getGatewayNames()
    {
        return array_keys($this->gateways);
    }

    public function getByName($name)
    {
        return isset($this->gateways[$name]) ? $this->gateways[$name] : null;
    }

    public function getByHandler($handler)
    {
        foreach ($this->gateways as $gateway) {
            if ($gateway->getHandler() == $handler) {
                return $gateway;
            }
        }

        return null;
    }

    public function get()
    {
        return $this->gateways;
    }
}
