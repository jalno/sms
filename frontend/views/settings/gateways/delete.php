<?php
namespace themes\clipone\views\sms\settings\gateways;
use \packages\base\translator;
use \packages\userpanel;
use \packages\sms\views\settings\gateways\delete as deleteView;
use \themes\clipone\viewTrait;
use \themes\clipone\navigation;

class delete extends deleteView{
	use viewTrait;
	function __beforeLoad(){
		$this->setTitle(translator::trans("settings.sms.gateways.delete"));
		navigation::active("settings/sms/gateways");
	}
	public function getGatewaysForSelect(){
		$options = array();
		foreach($this->getGateways()->get() as $gateway){
			$title = translator::trans('sms.gateway.'.$gateway->getName());
			$options[] = array(
				'value' => $gateway->getName(),
				'title' => $title ? $title : $gateway->getName()
			);
		}
		return $options;
	}
	public function getGatewayStatusForSelect(){
		$options = array(
			array(
				'title' => translator::trans('sms.gateway.status.active'),
				'value' => gateway::active
			),
			array(
				'title' => translator::trans('sms.gateway.status.deactive'),
				'value' => gateway::deactive
			)
		);
		return $options;
	}
}
