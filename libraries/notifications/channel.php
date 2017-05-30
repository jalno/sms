<?php
namespace packages\sms\notifications;
use \packages\base\event;
use \packages\notifications;
use \packages\sms\api;
class channel extends notifications\channel{
	public function notify(event $event){
		$api = new api();
		$api->template($event->getName(), $event->getArguments());
		foreach($event->getTargetUsers() as $user){
			$api->to($user->cellphone, $user);
			$api->send();
		}
	}
}