<?php
namespace themes\clipone\Views\SMS\Settings\GateWays;
use \packages\base\Translator;
use \packages\base\Events;
use \packages\base\Frontend\Theme;

use \packages\userpanel;
use \packages\sms\GateWay;
use \packages\sms\Views\Settings\GateWays\Add as AddView;

use \themes\clipone\ViewTrait;
use \themes\clipone\Navigation;
use \themes\clipone\Breadcrumb;
use \themes\clipone\Views\FormTrait;

class Add extends AddView{
	use ViewTrait, FormTrait;
	function __beforeLoad(){
		$this->setTitle(Translator::trans("settings.sms.gateways.add"));
		$this->setNavigation();
		$this->addAssets();
		$this->addBodyClass('sms_gateways');
	}
	public function addAssets(){
		$this->addCSSFile(Theme::url('assets/css/pages/add.css'));
	}
	private function setNavigation(){
		$add = new Navigation\MenuItem("gateway_add");
		$add->setTitle(Translator::trans('add'));
		$add->setIcon('fa fa-plus');
		$add->setURL(userpanel\url('settings/sms/gateways/add'));
		//breadcrumb::addItem($add);
		Navigation::active("settings/sms/gateways");
	}
	public function getGatewaysForSelect(){
		$options = array();
		foreach($this->getGateways()->get() as $gateway){
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
				'value' => GateWay::deactive
			)
		);
		return $options;
	}
}
