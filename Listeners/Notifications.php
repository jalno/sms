<?php

namespace packages\sms\Listeners;

use packages\notifications\Events\Channels;
use packages\sms\Notifications\Channel;

class Notifications
{
    public function channels(Channels $channels): void
    {
        $channels->add(Channel::class);
    }
}
