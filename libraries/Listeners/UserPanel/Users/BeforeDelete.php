<?php

namespace packages\sms\Listeners\UserPanel\Users;

use packages\base\{View\Error};
use packages\sms\Authorization;
use packages\sms\Get;
use packages\sms\Sent;
use packages\userpanel\Events as UserPanelEvents;

use function packages\userpanel\url;

class BeforeDelete
{
    public function check(UserPanelEvents\Users\BeforeDelete $event): void
    {
        $this->checkSentSMSesSender($event);
        $this->checkSentSMSesReceiver($event);
        $this->checkGetSMSesReceiver($event);
    }

    private function checkSentSMSesSender(UserPanelEvents\Users\BeforeDelete $event): void
    {
        $user = $event->getUser();
        $hasSentSMSes = (new Sent())->where('sender_user', $user->id)->has();
        if (!$hasSentSMSes) {
            return;
        }
        $message = t('error.packages.sms.error.smses.sent.sender_user.delete_user_warn.message');
        $error = new Error('packages.sms.error.smses.sent.sender_user.delete_user_warn');
        $error->setType(Error::WARNING);
        if (Authorization::is_accessed('sent_list')) {
            $message .= '<br> '.t('packages.sms.error.smses.sent.sender_user.delete_user_warn.view_smses').' ';
            $error->setData([
                [
                    'txt' => '<i class="fa fa-search"></i> '.t('packages.sms.error.smses.sent.sender_user.delete_user_warn.view_smses_btn'),
                    'type' => 'btn-warning',
                    'link' => url('sms/sent', [
                        'sender_user' => $user->id,
                    ]),
                ],
            ], 'btns');
        } else {
            $message .= '<br> '.t('packages.sms.error.smses.sent.sender_user.delete_user_warn.view_smses.sent.tell_someone');
        }
        $error->setMessage($message);

        $event->addError($error);
    }

    private function checkSentSMSesReceiver(UserPanelEvents\Users\BeforeDelete $event): void
    {
        $user = $event->getUser();
        $hasSentSMSes = (new Sent())->where('receiver_user', $user->id)->has();
        if (!$hasSentSMSes) {
            return;
        }
        $message = t('error.packages.sms.error.smses.sent.receiver_user.delete_user_warn.message');
        $error = new Error('packages.sms.error.smses.sent.receiver_user.delete_user_warn');
        $error->setType(Error::WARNING);
        if (Authorization::is_accessed('sent_list')) {
            $message .= '<br> '.t('packages.sms.error.smses.sent.receiver_user.delete_user_warn.view_smses').' ';
            $error->setData([
                [
                    'txt' => '<i class="fa fa-search"></i> '.t('packages.sms.error.smses.sent.receiver_user.delete_user_warn.view_smses_btn'),
                    'type' => 'btn-warning',
                    'link' => url('sms/sent', [
                        'receiver_user' => $user->id,
                    ]),
                ],
            ], 'btns');
        } else {
            $message .= '<br> '.t('packages.sms.error.smses.sent.receiver_user.delete_user_warn.view_smses.tell_someone');
        }
        $error->setMessage($message);

        $event->addError($error);
    }

    private function checkGetSMSesReceiver(UserPanelEvents\Users\BeforeDelete $event): void
    {
        $user = $event->getUser();
        $hasGetSMSes = (new Get())->where('sender_user', $user->id)->has();
        if (!$hasGetSMSes) {
            return;
        }
        $message = t('error.packages.sms.error.smses.get.sender_user.delete_user_warn.message');
        $error = new Error('packages.sms.error.smses.get.sender_user.delete_user_warn');
        $error->setType(Error::WARNING);
        if (Authorization::is_accessed('get_list')) {
            $message .= '<br> '.t('packages.sms.error.smses.get.sender_user.delete_user_warn.view_smses').' ';
            $error->setData([
                [
                    'txt' => '<i class="fa fa-search"></i> '.t('packages.sms.error.smses.get.sender_user.delete_user_warn.view_smses_btn'),
                    'type' => 'btn-warning',
                    'link' => url('sms/sent', [
                        'sender_user' => $user->id,
                    ]),
                ],
            ], 'btns');
        } else {
            $message .= '<br> '.t('packages.sms.error.smses.get.sender_user.delete_user_warn.view_smses.tell_someone');
        }
        $error->setMessage($message);

        $event->addError($error);
    }
}
