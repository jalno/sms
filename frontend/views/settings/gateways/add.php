<?php
namespace themes\clipone\views\sms\settings\gateways;
use \packages\base\translator;
use \packages\base\events;
use \packages\base\frontend\theme;

use \packages\userpanel;
use \packages\sms\gateway;
use \packages\sms\views\settings\gateways\add as addView;

use \themes\clipone\viewTrait;
use \themes\clipone\navigation;
use \themes\clipone\breadcrumb;
use \themes\clipone\views\formTrait;

class add extends addView{
	use viewTrait, formTrait;
	function __beforeLoad(){
		$this->setTitle(translator::trans("settings.sms.gateways.add"));
		$this->setNavigation();
		$this->addAssets();
		$this->addBodyClass('sms_gateways');
	}
	public function addAssets(){
		$this->addCSSFile(theme::url('assets/css/pages/add.css'));
	}
	private function setNavigation(){
		$add = new navigation\menuItem("gateway_add");
		$add->setTitle(translator::trans('add'));
		$add->setIcon('fa fa-plus');
		$add->setURL(userpanel\url('settings/sms/gateways/add'));
		//breadcrumb::addItem($add);
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
