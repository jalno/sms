<?php
namespace themes\clipone\views\sms\settings\templates;
use \packages\base\translator;
use \packages\base\events;
use \packages\base\frontend\theme;

use \packages\userpanel;
use \packages\sms\template;
use \packages\sms\views\settings\templates\add as addView;

use \themes\clipone\viewTrait;
use \themes\clipone\navigation;
use \themes\clipone\breadcrumb;
use \themes\clipone\views\formTrait;

class add extends addView{
	use viewTrait, formTrait;
	function __beforeLoad(){
		$this->setTitle(translator::trans("settings.sms.templates.add"));
		$this->setNavigation();
		$this->addBodyClass('sms_templates');
		if(!$this->getDataForm('lang')){
			$this->setDataForm('fa', 'lang');
		}
	}
	private function setNavigation(){
		$add = new navigation\menuItem("template_add");
		$add->setTitle(translator::trans('add'));
		$add->setIcon('fa fa-plus');
		$add->setURL(userpanel\url('settings/sms/templates/add'));
		//breadcrumb::addItem($add);
		navigation::active("settings/sms/templates");
	}
	public function getTemplatesForSelect(){
		$options = array();
		foreach($this->getTemplates() as $template){
			$title = translator::trans('sms.template.name.'.$template->name);
			$variables = array();
			foreach($template->variables as $variable){
				$description = '';
				$name = explode("->", $variable);
				for($x=0;$x!=count($name) and !$description;$x++){
					$variable_name = implode('->', array_slice($name,$x));
					$description = translator::trans('sms.template.variable.'.$variable_name);
				}
				$variables[] = array(
					'key' => $variable,
					'description' => (string)$description
				);
			}
			$options[] = array(
				'value' => $template->name,
				'title' => $title ? $title : $template->name,
				'data' => array(
					'variables' => $variables
				)
			);
		}
		return $options;
	}
	public function getTemplateStatusForSelect(){
		$options = array(
			array(
				'title' => translator::trans('sms.template.status.active'),
				'value' => template::active
			),
			array(
				'title' => translator::trans('sms.template.status.deactive'),
				'value' => template::deactive
			)
		);
		return $options;
	}
	public function getLanguagesForSelect(){
		$options = array();
		foreach(translator::$allowlangs as $lang){
			$options[] = array(
				'title' => translator::trans('translations.langs.'.$lang),
				'value' => $lang
			);
		}
		return $options;
	}
}
