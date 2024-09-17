<?php

namespace themes\clipone\Views\SMS\Settings\GateWays;

use packages\base\Frontend\Theme;
use packages\base\Translator;
use packages\sms\GateWay;
use packages\sms\Views\Settings\GateWays\Add as AddView;
use packages\userpanel;
use themes\clipone\Breadcrumb;
use themes\clipone\Navigation;
use themes\clipone\Views\FormTrait;
use themes\clipone\ViewTrait;

class Add extends AddView
{
    use ViewTrait;
    use FormTrait;

    public function __beforeLoad()
    {
        $this->setTitle(t('settings.sms.gateways.add'));
        $this->setNavigation();
        $this->addAssets();
        $this->addBodyClass('sms_gateways');
    }

    public function addAssets()
    {
        $this->addCSSFile(Theme::url('assets/css/pages/add.css'));
    }

    private function setNavigation()
    {
        $add = new Navigation\MenuItem('gateway_add');
        $add->setTitle(t('add'));
        $add->setIcon('fa fa-plus');
        $add->setURL(userpanel\url('settings/sms/gateways/add'));
        // breadcrumb::addItem($add);
        Navigation::active('settings/sms/gateways');
    }

    public function getGatewaysForSelect()
    {
        $options = [];
        foreach ($this->getGateways()->get() as $gateway) {
            $title = t('sms.gateway.'.$gateway->getName());
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
                'title' => t('sms.gateway.status.active'),
                'value' => GateWay::active,
            ],
            [
                'title' => t('sms.gateway.status.deactive'),
                'value' => GateWay::deactive,
            ],
        ];

        return $options;
    }
}
