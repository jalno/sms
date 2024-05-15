<?php
namespace packages\sms\Events;
use \packages\base\Event;
use \packages\sms\Sent;
class Send extends Event{
	protected $sms;
	public function __construct(Sent $sms){
		$this->sms = $sms;
	}
	public function getSms(){
		return $this->sms;
	}
}
