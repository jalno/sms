<?php

namespace themes\clipone\Views\SMS;

use packages\base\Translator;
use packages\sms\Views\Send as SMSend;
use packages\userpanel;
use themes\clipone\Breadcrumb;
use themes\clipone\Navigation;
use themes\clipone\Navigation\MenuItem;
use themes\clipone\Views\FormTrait;
use themes\clipone\ViewTrait;

class Send extends SMSend
{
    use ViewTrait;
    use FormTrait;

    public function __beforeLoad()
    {
        $this->setTitle(Translator::trans('sms.send'));
        $this->setNavigation();
        $this->addBodyClass('smsSend');
    }

    protected function getNumbersForSelect()
    {
        $options = [];
        foreach ($this->getNumbers() as $number) {
            $options[] = [
                'title' => $number->number,
                'value' => $number->id,
            ];
        }

        return $options;
    }

    protected function setNavigation()
    {
        $item = new MenuItem('sms');
        $item->setTitle(Translator::trans('smses'));
        $item->setIcon('fa fa-envelope');
        Breadcrumb::addItem($item);

        $item = new MenuItem('send');
        $item->setTitle(Translator::trans('sms.send'));
        $item->setURL(userpanel\url('sms/send'));
        Breadcrumb::addItem($item);
        Navigation::active('sms/send');
    }
}
