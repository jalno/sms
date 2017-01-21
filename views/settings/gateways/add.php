<?php
namespace packages\sms\views\settings\gateways;
use \packages\sms\events\gateways;
use \packages\userpanel\views\form;
class add extends form{
	public function setGateways(gateways $gateways){
		$this->setData($gateways, "gateways");
	}
	protected function getGateways(){
		return $this->getData('gateways');
	}
}
