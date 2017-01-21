<?php
namespace packages\sms\events;
use \packages\base\event;
use \packages\sms\get;
class receive extends event{
	protected $sms;
	public function __construct(get $sms){
		$this->sms = $sms;
	}
	public function getSms(){
		return $this->sms;
	}
}
