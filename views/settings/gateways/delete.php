<?php
namespace packages\sms\views\settings\gateways;
use \packages\sms\gateway;
use \packages\userpanel\views\form;
class delete extends form{
	public function setGateway(gateway $gateway){
		$this->setData($gateway, "gateway");
	}
	protected function getGateway(){
		return $this->getData('gateway');
	}
}
