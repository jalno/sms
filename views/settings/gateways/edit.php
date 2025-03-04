<?php
namespace packages\sms\views\settings\gateways;
use \packages\sms\gateway;
use \packages\sms\events\gateways;
use \packages\userpanel\views\form;
class edit extends form{
	public function setGateways($gateways){
		$this->setData($gateways, "gateways");
	}
	protected function getGateways(){
		return $this->getData('gateways');
	}
	public function setGateway(gateway $gateway){
		$this->setData($gateway, "gateway");
		$this->setDataForm($gateway->toArray());
		foreach($gateway->params as $param){
			$this->setDataForm($param->value, $param->name);
		}
		foreach($this->getGateways() as $g){
			if($g->getHandler() == $gateway->handler){
				$this->setDataForm($g->getName(), "gateway");
				break;
			}
		}
	}
	protected function getGateway(){
		return $this->getData('gateway');
	}
}
