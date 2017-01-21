<?php
namespace packages\sms\views;
use \packages\sms\views\form;
class send extends form{
	public function setNumbers($numbers){
		$this->setData($numbers,'numbers');
	}
	protected function getNumbers(){
		return $this->getData('numbers');
	}
}
