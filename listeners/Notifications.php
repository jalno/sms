<?php
namespace packages\sms\listeners;

use packages\notifications\events\Channels;
use packages\sms\notifications\Channel;

class Notifications {
	public function channels(Channels $channels): void {
		$channels->add(Channel::class);
	}
}
