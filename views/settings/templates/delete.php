<?php
namespace packages\sms\views\settings\templates;
use \packages\sms\template;
use \packages\userpanel\views\form;
class delete extends form{
	public function setTemplate(template $template){
		$this->setData($template, "template");
	}
	protected function getTemplate(){
		return $this->getData('template');
	}
}
