<?php

namespace packages\sms\Views\Settings\GateWays;

use packages\sms\GateWay;
use packages\userpanel\Views\Form;

class Delete extends Form
{
    public function setGateway(GateWay $gateway)
    {
        $this->setData($gateway, 'gateway');
    }

    protected function getGateway()
    {
        return $this->getData('gateway');
    }
}
