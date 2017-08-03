<?php
namespace packages\sms\notifications;
use \packages\base\event;
use \packages\base\translator;
use \packages\notifications;
use \packages\sms\api;
use \packages\sms\template;
use \packages\sms\deactivedNumberException;
use \packages\sms\defaultNumberException;
class channel extends notifications\channel{
	public function notify(event $event){
		$lang = translator::getShortCodeLang();
		$template = new template();
		$template->where('name', $event->getName());
		$template->where('lang', $lang);
		if($template->has()){
			try{
				foreach($event->getTargetUsers() as $user){
					$api = new api();
					$arguments = array_replace(array('user' => $user), $event->getArguments());
					$api->template($event->getName(), $arguments);
					$api->to($user->cellphone, $user);
					$api->send();
				}
			}catch(deactivedNumberException $e){

			}catch(defaultNumberException $e){
				
			}
		}
	}
}