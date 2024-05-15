<?php
namespace packages\sms\Views\Settings\Templates;
use \packages\sms\Events\Templates;
use \packages\userpanel\Views\Form;
class Add extends Form{
	public function getTemplates(){
		return $this->getData('templates');
	}
	public function setTemplates($templates){
		$this->setData($templates, 'templates');
	}
}
