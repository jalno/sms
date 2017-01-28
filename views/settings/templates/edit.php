<?php
namespace packages\sms\views\settings\templates;
use \packages\sms\template;
use \packages\userpanel\views\form;
class edit extends form{
	public function setTemplates($templates){
		$this->setData($templates, "templates");
	}
	protected function getTemplates(){
		return $this->getData('templates');
	}
	public function setTemplate(template $template){
		$this->setData($template, "template");
		$this->setDataForm($template->toArray());
	}
	protected function getTemplate(){
		return $this->getData('template');
	}
}
