<?php
namespace packages\sms\listeners;
use \packages\notifications\events\channels;
use \packages\sms\notifications\channel;
class notifications{
	public function channels(channels $channels){
		$channels->add(channel::class);
	}
}
