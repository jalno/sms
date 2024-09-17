<?php

namespace packages\sms\Views\Settings\GateWays;

use packages\base\Views\Traits\Form as FormTrait;
use packages\sms\Authorization;
use packages\sms\Events\GateWays;

class ListView extends \packages\userpanel\Views\ListView
{
    use FormTrait;
    protected $canAdd;
    protected $canEdit;
    protected $canDel;

    public function __construct()
    {
        $this->canAdd = Authorization::is_accessed('settings_gateways_add');
        $this->canEdit = Authorization::is_accessed('settings_gateways_edit');
        $this->canDel = Authorization::is_accessed('settings_gateways_delete');
    }

    public function getGateways()
    {
        return $this->getData('gateways');
    }

    public function setGateways(GateWays $gateways)
    {
        $this->setData($gateways, 'gateways');
    }
}
