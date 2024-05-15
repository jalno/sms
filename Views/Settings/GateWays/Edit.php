<?php

namespace packages\sms\Views\Settings\GateWays;

use packages\sms\GateWay;
use packages\userpanel\Views\Form;

class Edit extends Form
{
    public function setGateways($gateways)
    {
        $this->setData($gateways, 'gateways');
    }

    protected function getGateways()
    {
        return $this->getData('gateways');
    }

    public function setGateway(GateWay $gateway)
    {
        $this->setData($gateway, 'gateway');
        $this->setDataForm($gateway->toArray());
        foreach ($gateway->params as $param) {
            $this->setDataForm($param->value, $param->name);
        }
        foreach ($this->getGateways() as $g) {
            if ($g->getHandler() == $gateway->handler) {
                $this->setDataForm($g->getName(), 'gateway');
                break;
            }
        }
    }

    protected function getGateway()
    {
        return $this->getData('gateway');
    }
}
