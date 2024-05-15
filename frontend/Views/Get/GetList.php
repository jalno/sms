<?php
namespace themes\clipone\Views\SMS\Get;
use \packages\base\Translator;
use \packages\base\Frontend\Theme;
use \packages\userpanel;
use \packages\userpanel\User;
use \packages\sms\Get;
use \packages\sms\Views\Get\ListView as GetList;
use \themes\clipone\Navigation;
use \themes\clipone\Navigation\MenuItem;
use \themes\clipone\Views\ListTrait;
use \themes\clipone\Views\FormTrait;
use \themes\clipone\ViewTrait;

class ListView extends GetList{
	use ViewTrait,ListTrait, FormTrait;
	function __beforeLoad(){
		$this->setTitle(Translator::trans('sms.get'));
		Navigation::active("sms/get");
		$this->addBodyClass('smslist');
		$this->setUserInput();
	}
	protected function getStatusForSelect(){
		return array(
			array(
				'title' => Translator::trans("choose"),
				'value' => ''
			),
			array(
				'title' => Translator::trans("sms.get.status.unread"),
				'value' => Get::unread
			),
			array(
				'title' => Translator::trans("sms.get.status.read"),
				'value' => Get::read
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
		if($error = $this->getFormErrorsByInput('sender_user')){
			$error->setInput('sender_user_name');
			$this->setFormError($error);
		}
		$user = $this->getDataForm('sender_user');
		if($user and $user = User::byId($user)){
			$this->setDataForm($user->name, 'sender_user_name');
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
			$get = new MenuItem("get");
			$get->setTitle(Translator::trans('sms.get'));
			$get->setURL(userpanel\url('sms/get'));
			$get->setIcon('clip-download');
			$sms->addItem($get);
		}
	}
}
