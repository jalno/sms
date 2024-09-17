<?php

namespace themes\clipone\Views\SMS\Sent;

use packages\base\Translator;
use packages\sms\Sent;
use packages\sms\Views\Sent\ListView as SentList;
use packages\userpanel;
use packages\userpanel\User;
use themes\clipone\Navigation;
use themes\clipone\Navigation\MenuItem;
use themes\clipone\Views\FormTrait;
use themes\clipone\Views\ListTrait;
use themes\clipone\ViewTrait;

class ListView extends SentList
{
    use ViewTrait;
    use ListTrait;
    use FormTrait;

    public function __beforeLoad()
    {
        $this->setTitle(t('sms.sent'));
        Navigation::active('sms/sent');
        $this->addBodyClass('smslist');
        $this->setUserInput();
    }

    public function getStatusForSelect()
    {
        return [
            [
                'title' => t('choose'),
                'value' => '',
            ],
            [
                'title' => t('sms.sent.status.queued'),
                'value' => Sent::queued,
            ],
            [
                'title' => t('sms.sent.status.sending'),
                'value' => Sent::sending,
            ],
            [
                'title' => t('sms.sent.status.sent'),
                'value' => Sent::sent,
            ],
            [
                'title' => t('sms.sent.status.failed'),
                'value' => Sent::failed,
            ],
        ];
    }

    public function getComparisonsForSelect()
    {
        return [
            [
                'title' => t('search.comparison.contains'),
                'value' => 'contains',
            ],
            [
                'title' => t('search.comparison.equals'),
                'value' => 'equals',
            ],
            [
                'title' => t('search.comparison.startswith'),
                'value' => 'startswith',
            ],
        ];
    }

    private function setUserInput()
    {
        foreach (['sender_user', 'receiver_user'] as $field) {
            if ($error = $this->getFormErrorsByInput($field)) {
                $error->setInput($field.'_name');
                $this->setFormError($error);
            }
            $user = $this->getDataForm($field);
            if ($user and $user = User::byId($user)) {
                $this->setDataForm($user->name, $field.'_name');
            }
        }
    }

    public static function onSourceLoad()
    {
        parent::onSourceLoad();
        if (parent::$navigation) {
            if (!$sms = Navigation::getByName('sms')) {
                $sms = new MenuItem('sms');
                $sms->setTitle(t('smses'));
                $sms->setIcon('fa fa-envelope');
                Navigation::addItem($sms);
            }
            $sent = new MenuItem('sent');
            $sent->setTitle(t('sms.sent'));
            $sent->setURL(userpanel\url('sms/sent'));
            $sent->setIcon('clip-upload');
            $sms->addItem($sent);
        }
    }
}
