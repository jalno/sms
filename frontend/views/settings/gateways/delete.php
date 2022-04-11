<?php
namespace themes\clipone\views\sms\settings\gateways;
use \packages\base\translator;
use \packages\userpanel;
use \packages\sms\views\settings\gateways\delete as deleteView;
use \themes\clipone\viewTrait;
use \themes\clipone\navigation;

class delete extends deleteView{
	use viewTrait;

	public function __beforeLoad() {
		$this->setTitle(translator::trans("settings.sms.gateways.delete"));
		navigation::active("settings/sms/gateways");
	}
}
