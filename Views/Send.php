<?php
namespace packages\sms\Views;
use \packages\sms\Views\Form;
class Send extends Form{
	public function setNumbers($numbers){
		$this->setData($numbers,'numbers');
	}
	protected function getNumbers(){
		return $this->getData('numbers');
	}
}
