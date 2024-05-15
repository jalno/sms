<?php
namespace themes\clipone\Views\SMS\Settings\GateWays;
use \packages\base\Translator;
use \packages\userpanel;
use \packages\sms\Views\Settings\GateWays\Delete as DeleteView;
use \themes\clipone\ViewTrait;
use \themes\clipone\Navigation;

class Delete extends DeleteView{
	use ViewTrait;

	public function __beforeLoad() {
		$this->setTitle(Translator::trans("settings.sms.gateways.delete"));
		Navigation::active("settings/sms/gateways");
	}
}
