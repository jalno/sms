<?php
namespace themes\clipone\Views\SMS\Sent;

use \packages\base\Translator;
use \packages\base\Frontend\Theme;
use \packages\userpanel;
use \packages\userpanel\User;
use \packages\sms\Sent;
use \packages\sms\Views\Sent\ListView as SentList;
use \themes\clipone\Navigation;
use \themes\clipone\Navigation\MenuItem;
use themes\clipone\Views\ListTrait;
use themes\clipone\Views\FormTrait;
use themes\clipone\ViewTrait;

class ListView extends SentList {
	use ViewTrait, ListTrait, FormTrait;

	public function __beforeLoad(){
		$this->setTitle(Translator::trans('sms.sent'));
		Navigation::active("sms/sent");
		$this->addBodyClass('smslist');
		$this->setUserInput();
	}
	public function getStatusForSelect(){
		return array(
			array(
				'title' => Translator::trans("choose"),
				'value' => ''
			),
			array(
				'title' => Translator::trans("sms.sent.status.queued"),
				'value' => Sent::queued
			),
			array(
				'title' => Translator::trans("sms.sent.status.sending"),
				'value' => Sent::sending
			),
			array(
				'title' => Translator::trans("sms.sent.status.sent"),
				'value' => Sent::sent
			),
			array(
				'title' => Translator::trans("sms.sent.status.failed"),
				'value' => Sent::failed
			)
		);
	}
	public function getComparisonsForSelect(){
		return array(
			array(
				'title' => Translator::trans('search.comparison.contains'),
				'value' => 'contains'
			),
			array(
				'title' => Translator::trans('search.comparison.equals'),
				'value' => 'equals'
			),
			array(
				'title' => Translator::trans('search.comparison.startswith'),
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
			if(!$sms = Navigation::getByName('sms')){
				$sms = new MenuItem("sms");
				$sms->setTitle(Translator::trans('smses'));
				$sms->setIcon('fa fa-envelope');
				Navigation::addItem($sms);
			}
			$sent = new MenuItem("sent");
			$sent->setTitle(Translator::trans('sms.sent'));
			$sent->setURL(userpanel\url('sms/sent'));
			$sent->setIcon('clip-upload');
			$sms->addItem($sent);
		}
	}
}
