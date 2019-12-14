<?php
namespace packages\sms\notifications;

use packages\base\{Event, Translator};
use packages\notifications;
use packages\sms\{API, Template, DeactivedNumberException, DefaultNumberException};

class Channel extends notifications\Channel {

	public function notify(Event $event) {
		$lang = Translator::getShortCodeLang();
		$template = new Template();
		$template->where('name', $event->getName());
		$template->where('lang', $lang);
		$template->where('status', Template::active);
		if ($template->has()) {
			try {
				foreach ($event->getTargetUsers() as $user) {
					$api = new API();
					$arguments = array_replace(array('user' => $user), $event->getArguments());
					$api->template($event->getName(), $arguments);
					$api->to($user->cellphone, $user);
					$api->send();
				}
			} catch (DeactivedNumberException $e) {
			} catch (DefaultNumberException $e) {
			}
		}
	}

	public function getName(): string {
		return "sms";
	}
}