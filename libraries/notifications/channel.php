<?php
namespace packages\sms\notifications;
use \packages\base\event;
use \packages\notifications;
use \packages\sms\api;
class channel extends notifications\channel{
	public function notify(event $event){
		foreach($event->getTargetUsers() as $user){
			$api = new api();
			$arguments = array_replace(array('user' => $user), $event->getArguments());
			$api->template($event->getName(), $arguments);
			$api->to($user->cellphone, $user);
			$api->send();
		}
	}
}