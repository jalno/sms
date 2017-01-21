<?php
namespace packages\sms\views\settings\gateways;
use \packages\userpanel\views\listview as list_view;
use \packages\sms\authorization;
use \packages\sms\events\gateways;
use \packages\base\views\traits\form as formTrait;
class listview extends list_view{
	use formTrait;
	protected $canAdd;
	protected $canEdit;
	protected $canDel;
	static protected $navigation;
	function __construct(){
		$this->canAdd = authorization::is_accessed('settings_gateways_add');
		$this->canEdit = authorization::is_accessed('settings_gateways_edit');
		$this->canDel = authorization::is_accessed('settings_gateways_delete');
	}
	public function getGateways(){
		return $this->getData('gateways');
	}
	public function setGateways(gateways $gateways){
		$this->setData($gateways, 'gateways');
	}
	public static function onSourceLoad(){
		self::$navigation = authorization::is_accessed('settings_gateways_list');
	}
}
