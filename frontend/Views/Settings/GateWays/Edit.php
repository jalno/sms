<?php

namespace themes\clipone\Views\SMS\Settings\GateWays;

use packages\base\Options;
use packages\base\Translator;
use packages\sms\GateWay;
use packages\sms\Views\Settings\GateWays\Edit as EditView;
use themes\clipone\Navigation;
use themes\clipone\Views\FormTrait;
use themes\clipone\ViewTrait;

class Edit extends EditView
{
    use ViewTrait;
    use FormTrait;

    public function __beforeLoad()
    {
        $this->setTitle(Translator::trans('settings.sms.gateways.edit'));
        $this->setNavigation();
        $this->addBodyClass('sms_gateways');
    }

    private function setNavigation()
    {
        Navigation::active('settings/sms/gateways');
    }

    public function getGatewaysForSelect()
    {
        $options = [];
        foreach ($this->getGateways() as $gateway) {
            $title = Translator::trans('sms.gateway.'.$gateway->getName());
            $options[] = [
                'value' => $gateway->getName(),
                'title' => $title ? $title : $gateway->getName(),
            ];
        }

        return $options;
    }

    public function getGatewayStatusForSelect()
    {
        $options = [
            [
                'title' => Translator::trans('sms.gateway.status.active'),
                'value' => GateWay::active,
            ],
            [
                'title' => Translator::trans('sms.gateway.status.deactive'),
                'value' => GateWay::deactive,
            ],
        ];

        return $options;
    }

    protected function getNumbersData()
    {
        $numbers = [];
        foreach ($this->getGateway()->numbers as $number) {
            $numberData = $number->toArray();
            if (Options::get('packages.sms.defaultNumber') == $number->id) {
                $numberData['primary'] = true;
            }
            $numbers[] = $numberData;
        }

        return $numbers;
    }
}
