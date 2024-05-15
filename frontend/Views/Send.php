<?php
namespace themes\clipone\Views\SMS;
use \packages\base\Translator;
use \packages\userpanel;
use \packages\sms\Views\Send as SMSend;
use \themes\clipone\Navigation;
use \themes\clipone\Breadcrumb;
use \themes\clipone\Navigation\MenuItem;
use \themes\clipone\ViewTrait;
use \themes\clipone\Views\FormTrait;

class Send extends SMSend{
	use ViewTrait,FormTrait;
	function __beforeLoad(){
		$this->setTitle(Translator::trans('sms.send'));
		$this->setNavigation();
		$this->addBodyClass('smsSend');
	}
	protected function getNumbersForSelect(){
		$options = array();
		foreach($this->getNumbers() as $number){
			$options[] = array(
				'title' => $number->number,
				'value' => $number->id
			);
		}
		return $options;
	}
	protected function setNavigation(){
		$item = new MenuItem("sms");
		$item->setTitle(Translator::trans('smses'));
		$item->setIcon('fa fa-envelope');
		Breadcrumb::addItem($item);

		$item = new MenuItem("send");
		$item->setTitle(Translator::trans('sms.send'));
		$item->setURL(userpanel\url('sms/send'));
		Breadcrumb::addItem($item);
		Navigation::active("sms/send");
	}
}
