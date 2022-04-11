<?php
namespace themes\clipone\views\sms\sent;

use \packages\base\translator;
use \packages\base\frontend\theme;
use \packages\userpanel;
use \packages\userpanel\user;
use \packages\sms\sent;
use \packages\sms\views\sent\listview as sentList;
use \themes\clipone\navigation;
use \themes\clipone\navigation\menuItem;
use themes\clipone\views\ListTrait;
use themes\clipone\views\FormTrait;
use themes\clipone\ViewTrait;

class listview extends sentList {
	use ViewTrait, ListTrait, FormTrait;

	public function __beforeLoad(){
		$this->setTitle(translator::trans('sms.sent'));
		navigation::active("sms/sent");
		$this->addBodyClass('smslist');
		$this->setUserInput();
	}
	public function getStatusForSelect(){
		return array(
			array(
				'title' => translator::trans("choose"),
				'value' => ''
			),
			array(
				'title' => translator::trans("sms.sent.status.queued"),
				'value' => sent::queued
			),
			array(
				'title' => translator::trans("sms.sent.status.sending"),
				'value' => sent::sending
			),
			array(
				'title' => translator::trans("sms.sent.status.sent"),
				'value' => sent::sent
			),
			array(
				'title' => translator::trans("sms.sent.status.failed"),
				'value' => sent::failed
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
		foreach(array('sender_user', 'receiver_user') as $field){
			if($error = $this->getFormErrorsByInput($field)){
				$error->setInput($field.'_name');
				$this->setFormError($error);
			}
			$user = $this->getDataForm($field);
			if($user and $user = user::byId($user)){
				$this->setDataForm($user->name, $field.'_name');
			}
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
			$sent = new menuItem("sent");
			$sent->setTitle(translator::trans('sms.sent'));
			$sent->setURL(userpanel\url('sms/sent'));
			$sent->setIcon('clip-upload');
			$sms->addItem($sent);
		}
	}
}
