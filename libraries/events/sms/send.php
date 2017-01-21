<?php
namespace packages\sms\events;
use \packages\base\event;
use \packages\sms\sent;
class send extends event{
	protected $sms;
	public function __construct(sent $sms){
		$this->sms = $sms;
	}
	public function getSms(){
		return $this->sms;
	}
}
