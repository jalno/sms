<?php

namespace packages\sms\Notifications;

use packages\base\EventInterface;
use packages\base\Translator;
use packages\notifications\IChannel;
use packages\sms\API;
use packages\sms\DeactivedNumberException;
use packages\sms\DefaultNumberException;
use packages\sms\Template;

class Channel implements IChannel
{
    public function notify(EventInterface $event): void
    {
        if (!$this->canNotify($event)) {
            return;
        }
        try {
            foreach ($event->getTargetUsers() as $user) {
                $api = new API();
                $arguments = array_replace(['user' => $user], $event->getArguments());
                $api->template($event->getName(), $arguments);
                $api->to($user->cellphone, $user);
                $api->send();
            }
        } catch (DeactivedNumberException $e) {
        } catch (DefaultNumberException $e) {
        }
    }

    public function canNotify(EventInterface $event): bool
    {
        $lang = Translator::getShortCodeLang();
        $template = new Template();
        $template->where('name', $event->getName());
        $template->where('lang', $lang);
        $template->where('status', Template::active);

        return $template->has();
    }

    public function getName(): string
    {
        return 'sms';
    }
}
