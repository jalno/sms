<?php
namespace themes\clipone\views\sms;
use \packages\base\translator;
use \packages\userpanel;
use \packages\sms\views\send as smsend;
use \themes\clipone\navigation;
use \themes\clipone\breadcrumb;
use \themes\clipone\navigation\menuItem;
use \themes\clipone\viewTrait;
use \themes\clipone\views\formTrait;

class send extends smsend{
	use viewTrait,formTrait;
	function __beforeLoad(){
		$this->setTitle(translator::trans('sms.send'));
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
		$item = new menuItem("sms");
		$item->setTitle(translator::trans('smses'));
		$item->setIcon('fa fa-envelope');
		breadcrumb::addItem($item);

		$item = new menuItem("send");
		$item->setTitle(translator::trans('sms.send'));
		$item->setURL(userpanel\url('sms/send'));
		breadcrumb::addItem($item);
		navigation::active("sms/send");
	}
}
