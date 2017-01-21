<?php
namespace themes\clipone\views\sms\settings\gateways;
use \packages\base\translator;
use \packages\base\events;
use \packages\base\frontend\theme;
use \packages\base\options;

use \packages\userpanel;
use \packages\sms\gateway;
use \packages\sms\views\settings\gateways\edit as editView;

use \themes\clipone\viewTrait;
use \themes\clipone\navigation;
use \themes\clipone\breadcrumb;
use \themes\clipone\views\formTrait;

class edit extends editView{
	use viewTrait, formTrait;
	function __beforeLoad(){
		$this->setTitle(translator::trans("settings.sms.gateways.edit"));
		$this->setNavigation();
		$this->addAssets();
	}
	public function addAssets(){
		$this->addJSFile(theme::url('assets/plugins/jquery-validation/dist/jquery.validate.min.js'));
		$this->addJSFile(theme::url('assets/plugins/bootstrap-inputmsg/bootstrap-inputmsg.min.js'));
		$this->addJSFile(theme::url('assets/js/pages/add.js'));
		$this->addCSSFile(theme::url('assets/css/pages/add.css'));
	}
	private function setNavigation(){
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
	protected function getNumbersData(){
		$numbers = array();
		foreach($this->getGateway()->numbers as $number){
			$numberData = $number->toArray();
			if(options::get('packages.sms.defaultNumber') == $number->id){
				$numberData['primary'] = true;
			}
			$numbers[] = $numberData;
		}
		return $numbers;
	}
}
