<?php
namespace packages\sms\Views\Settings\GateWays;
use \packages\sms\Events\GateWays;
use \packages\userpanel\Views\Form;
class Add extends Form{
	public function setGateways(GateWays $gateways){
		$this->setData($gateways, "gateways");
	}
	protected function getGateways(){
		return $this->getData('gateways');
	}
}
