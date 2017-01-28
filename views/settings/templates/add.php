<?php
namespace packages\sms\views\settings\templates;
use \packages\sms\events\templates;
use \packages\userpanel\views\form;
class add extends form{
	public function getTemplates(){
		return $this->getData('templates');
	}
	public function setTemplates($templates){
		$this->setData($templates, 'templates');
	}
}
