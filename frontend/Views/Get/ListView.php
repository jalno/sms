<?php

namespace themes\clipone\Views\SMS\Get;

use packages\base\Translator;
use packages\sms\Get;
use packages\sms\Views\Get\ListView as GetList;
use packages\userpanel;
use packages\userpanel\User;
use themes\clipone\Navigation;
use themes\clipone\Navigation\MenuItem;
use themes\clipone\Views\FormTrait;
use themes\clipone\Views\ListTrait;
use themes\clipone\ViewTrait;

class ListView extends GetList
{
    use ViewTrait;
    use ListTrait;
    use FormTrait;

    public function __beforeLoad()
    {
        $this->setTitle(t('sms.get'));
        Navigation::active('sms/get');
        $this->addBodyClass('smslist');
        $this->setUserInput();
    }

    protected function getStatusForSelect()
    {
        return [
            [
                'title' => t('choose'),
                'value' => '',
            ],
            [
                'title' => t('sms.get.status.unread'),
                'value' => Get::unread,
            ],
            [
                'title' => t('sms.get.status.read'),
                'value' => Get::read,
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
        if ($error = $this->getFormErrorsByInput('sender_user')) {
            $error->setInput('sender_user_name');
            $this->setFormError($error);
        }
        $user = $this->getDataForm('sender_user');
        if ($user and $user = User::byId($user)) {
            $this->setDataForm($user->name, 'sender_user_name');
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
            $get = new MenuItem('get');
            $get->setTitle(t('sms.get'));
            $get->setURL(userpanel\url('sms/get'));
            $get->setIcon('clip-download');
            $sms->addItem($get);
        }
    }
}
