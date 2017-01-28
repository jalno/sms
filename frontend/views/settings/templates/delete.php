<?php
namespace themes\clipone\views\sms\settings\templates;
use \packages\base\translator;
use \packages\userpanel;
use \packages\sms\views\settings\templates\delete as deleteView;
use \themes\clipone\viewTrait;
use \themes\clipone\navigation;

class delete extends deleteView{
	use viewTrait;
	function __beforeLoad(){
		$this->setTitle(translator::trans("settings.sms.templates.delete"));
		navigation::active("settings/sms/templates");
	}
	public function gettemplatesForSelect(){
		$options = array();
		foreach($this->gettemplates()->get() as $template){
			$title = translator::trans('sms.template.'.$template->getName());
			$options[] = array(
				'value' => $template->getName(),
				'title' => $title ? $title : $template->getName()
			);
		}
		return $options;
	}
	public function gettemplateStatusForSelect(){
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
}
