<?php
namespace themes\clipone\views\sms\get;
use \packages\base\translator;
use \packages\base\frontend\theme;
use \packages\userpanel;
use \packages\userpanel\user;
use \packages\sms\get;
use \packages\sms\views\get\listview as getList;
use \themes\clipone\navigation;
use \themes\clipone\navigation\menuItem;
use \themes\clipone\views\listTrait;
use \themes\clipone\views\formTrait;
use \themes\clipone\viewTrait;

class listview extends getList{
	use viewTrait,listTrait, formTrait;
	function __beforeLoad(){
		$this->setTitle(translator::trans('sms.get'));
		navigation::active("sms/get");
		$this->addBodyClass('smslist');
		$this->setUserInput();
	}
	protected function getStatusForSelect(){
		return array(
			array(
				'title' => translator::trans("choose"),
				'value' => ''
			),
			array(
				'title' => translator::trans("sms.get.status.unread"),
				'value' => get::unread
			),
			array(
				'title' => translator::trans("sms.get.status.read"),
				'value' => get::read
			)
		);
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
	private function setUserInput(){
		if($error = $this->getFormErrorsByInput('sender_user')){
			$error->setInput('sender_user_name');
			$this->setFormError($error);
		}
		$user = $this->getDataForm('sender_user');
		if($user and $user = user::byId($user)){
			$this->setDataForm($user->name, 'sender_user_name');
		}
	}
	public static function onSourceLoad(){
		parent::onSourceLoad();
		if(parent::$navigation){
			if(!$sms = navigation::getByName('sms')){
				$sms = new menuItem("sms");
				$sms->setTitle(translator::trans('smses'));
				$sms->setIcon('fa fa-envelope');
				navigation::addItem($sms);
			}
			$get = new menuItem("get");
			$get->setTitle(translator::trans('sms.get'));
			$get->setURL(userpanel\url('sms/get'));
			$get->setIcon('clip-download');
			$sms->addItem($get);
		}
	}
}
