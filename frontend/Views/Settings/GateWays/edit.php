<?php
namespace themes\clipone\Views\SMS\Settings\GateWays;
use \packages\base\Translator;
use \packages\base\Events;
use \packages\base\Frontend\Theme;
use \packages\base\Options;

use \packages\userpanel;
use \packages\sms\GateWay;
use \packages\sms\Views\Settings\GateWays\Edit as EditView;

use \themes\clipone\ViewTrait;
use \themes\clipone\Navigation;
use \themes\clipone\Breadcrumb;
use \themes\clipone\Views\FormTrait;

class Edit extends EditView{
	use ViewTrait, FormTrait;
	function __beforeLoad(){
		$this->setTitle(Translator::trans("settings.sms.gateways.edit"));
		$this->setNavigation();
		$this->addBodyClass('sms_gateways');
	}
	private function setNavigation(){
		Navigation::active("settings/sms/gateways");
	}
	public function getGatewaysForSelect(){
		$options = array();
		foreach($this->getGateways() as $gateway){
			$title = Translator::trans('sms.gateway.'.$gateway->getName());
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
				'title' => Translator::trans('sms.gateway.status.active'),
				'value' => GateWay::active
			),
			array(
				'title' => Translator::trans('sms.gateway.status.deactive'),
				'value' => Gateway::deactive
			)
		);
		return $options;
	}
	protected function getNumbersData(){
		$numbers = array();
		foreach($this->getGateway()->numbers as $number){
			$numberData = $number->toArray();
			if(Options::get('packages.sms.defaultNumber') == $number->id){
				$numberData['primary'] = true;
			}
			$numbers[] = $numberData;
		}
		return $numbers;
	}
}
