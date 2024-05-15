<?php
namespace packages\sms\Events;
use \packages\base\Event;
use \packages\sms\Get;
class Receive extends Event{
	protected $sms;
	public function __construct(get $sms){
		$this->sms = $sms;
	}
	public function getSms(){
		return $this->sms;
	}
}
