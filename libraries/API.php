<?php

namespace packages\sms;

use packages\base\Events;
use packages\base\Options;
use packages\base\Translator;
use packages\base\Utility\Safe;
use packages\sms\Events as SMSEvents;
use packages\sms\GateWay\Number;
use packages\userpanel\User;

class API
{
    private $message;
    private $receiver_number;
    private $receiver_user;
    private $sender_user;
    private $sender_number;
    private $time;

    public function template($name, $parameters = [], $lang = null)
    {
        if (null === $lang) {
            $lang = app()->getLocale();
        }
        if (!$lang) {
            throw new UnKownLanguage();
        }
        $template = new Template();
        $template->where('name', $name);
        $template->where('lang', $lang);
        $template->where('status', Template::active);
        if ($template = $template->getOne()) {
            $this->message = $template->render($parameters);
        } else {
            $this->message = '';
        }

        return $this;
    }

    public function to($receiver_number, ?User $receiver_user = null)
    {
        $this->receiver_number = $receiver_number;
        if (null === $receiver_user and !is_object($receiver_number)) {
            $user = new User();
            $user->where('cellphone', $receiver_number);
            $receiver_user = $user->getOne();
        }
        if ($receiver_user) {
            $this->receiver_user = $receiver_user;
        }

        return $this;
    }

    public function fromUser(User $sender_user)
    {
        $this->sender_user = $sender_user;

        return $this;
    }

    public function fromNumber($number)
    {
        if (is_object($number) and $number instanceof Number) {
            if (Number::active == $number->status and GateWay::active == $number->gateway->status) {
                $this->sender_number = $number;
            } else {
                throw new DeactivedNumberException();
            }
        } else {
            $this->sender_number = $number;
        }

        return $this;
    }

    public function fromDefaultNumber()
    {
        if ($defaultnumber = Options::get('packages.sms.defaultNumber')) {
            $number = (new Number())->byID($defaultnumber);
            if ($number) {
                $this->fromNumber($number);
            } else {
                throw new DefaultNumberException();
            }
        } else {
            throw new DefaultNumberException();
        }
    }

    private function checkFromNumber($type)
    {
        if ('receive' == $type) {
            if (!Safe::is_cellphone_ir($this->sender_number)) {
                throw new NumberException();
            }
        } elseif ('send' == $type) {
            if (!$this->sender_number instanceof Number) {
                $sender_number = (new Number())->where('number', $this->sender_number)->getOne();
                if ($sender_number) {
                    if (Number::active == $sender_number->status and GateWay::active == $sender_number->gateway->status) {
                        $this->sender_number = $sender_number;
                    } else {
                        throw new DeactivedNumberException();
                    }
                } else {
                    throw new NumberException();
                }
            }
        }
    }

    private function checkToNumber($type)
    {
        if ('send' == $type) {
            if (!Safe::is_cellphone_ir($this->receiver_number)) {
                throw new NumberException();
            }
        } elseif ('receive' == $type) {
            if (!$this->receiver_number instanceof Number) {
                $receiver_number = (new Number())->where('number', $this->receiver_number)->getOne();
                if ($receiver_number) {
                    if (Number::active == $receiver_number->status and GateWay::active == $receiver_number->gateway->status) {
                        $this->receiver_number = $receiver_number;
                    } else {
                        throw new DeactivedNumberException();
                    }
                } else {
                    throw new NumberException();
                }
            }
        }
    }

    public function now()
    {
        $this->time = time();

        return $this;
    }

    public function at($time)
    {
        $this->time = $time;

        return $this;
    }

    public function receive($message)
    {
        $this->checkFromNumber('receive');
        $this->checkToNumber('receive');
        $sms = new Get();
        $sms->receive_at = $this->time;
        $sms->sender_number = $this->sender_number;
        if ($this->sender_user) {
            $sms->sender_user = $this->sender_user->id;
        }
        $sms->receiver_number = $this->receiver_number->id;
        $sms->text = $message;
        $sms->save();
        Events::trigger(new SMSEvents\Receive($sms));

        return true;
    }

    public function send($message = null)
    {
        $sms = new Sent();
        $sms->send_at = $this->time;
        if ($this->sender_number) {
            $this->checkFromNumber('send');
        } else {
            $this->fromDefaultNumber();
        }

        $sms->sender_number = $this->sender_number->id;
        if ($this->sender_user) {
            $sms->sender_user = $this->sender_user->id;
        }
        $sms->receiver_number = $this->receiver_number;
        if ($this->receiver_user) {
            $sms->receiver_user = $this->receiver_user->id;
        }
        $sms->text = null !== $message ? $message : $this->message;
        if ($sms->send_at >= time()) {
            $sms->status = Sent::queued;
        } else {
            $sms->status = Sent::sending;
        }
        $sms->save();
        if ($sms->send_at >= time()) {
            $sms->send();
        }
        Events::trigger(new SMSEvents\Send($sms));

        return $sms->status;
    }
}
