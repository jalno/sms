<?php
namespace themes\clipone\views\sms\settings\templates;
use \packages\base\translator;
use \packages\userpanel;
use \packages\sms\views\settings\templates\delete as deleteView;
use \themes\clipone\viewTrait;
use \themes\clipone\navigation;

class delete extends deleteView{
	use viewTrait;

	public function __beforeLoad() {
		$this->setTitle(translator::trans("settings.sms.templates.delete"));
		navigation::active("settings/sms/templates");
	}
}
