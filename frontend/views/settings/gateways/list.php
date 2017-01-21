<?php
namespace themes\clipone\views\sms\settings\gateways;

use \packages\base\translator;
use \packages\base\frontend\theme;

use \packages\userpanel;
use \themes\clipone\viewTrait;
use \themes\clipone\navigation;
use \themes\clipone\views\listTrait;
use \themes\clipone\views\formTrait;
use \themes\clipone\navigation\menuItem;

use \packages\sms\views\settings\gateways\listview as gatewaysListview;
use \packages\sms\gateway;

class listview extends gatewaysListview{
	use viewTrait, listTrait, formTrait;
	private $categories;
	function __beforeLoad(){
		$this->setTitle(translator::trans("settings.sms.gateways"));
		navigation::active("settings/sms/gateways");
		$this->setButtons();
		$this->addAssets();
	}
	private function addAssets(){

	}
	public function getComparisonsForSelect(){
		return array(
			array(
				'title' => translator::trans('search.comparison.contains'),
				'value' => 'contains'
			),
			array(
				'title' => translator::trans('search.comparison.equals'),
				'value' => 'equals'
			),
			array(
				'title' => translator::trans('search.comparison.startswith'),
				'value' => 'startswith'
			)
		);
	}
	public static function onSourceLoad(){
		parent::onSourceLoad();
		if(parent::$navigation){
			$settings = navigation::getByName("settings");
			if(!$sms = navigation::getByName("settings/sms")){
				$sms = new menuItem("sms");
				$sms->setTitle(translator::trans('settings.sms'));
				$sms->setIcon("fa fa-envelope");
				if($settings)$settings->addItem($sms);
			}
			$gateways = new menuItem("gateways");
			$gateways->setTitle(translator::trans('settings.sms.gateways'));
			$gateways->setURL(userpanel\url('settings/sms/gateways'));
			$gateways->setIcon('fa fa-rss');
			$sms->addItem($gateways);
		}
	}
	public function setButtons(){
		$this->setButton('edit', $this->canEdit, array(
			'title' => translator::trans('edit'),
			'icon' => 'fa fa-edit',
			'classes' => array('btn', 'btn-xs', 'btn-warning')
		));
		$this->setButton('delete', $this->canDel, array(
			'title' => translator::trans('delete'),
			'icon' => 'fa fa-times',
			'classes' => array('btn', 'btn-xs', 'btn-bricky')
		));
	}
}
