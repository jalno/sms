<?php

namespace themes\clipone\Views\SMS\Settings\Templates;

use packages\base\Translator;
use packages\sms\Views\Settings\Templates\Delete as DeleteView;
use themes\clipone\Navigation;
use themes\clipone\ViewTrait;

class Delete extends DeleteView
{
    use ViewTrait;

    public function __beforeLoad()
    {
        $this->setTitle(t('settings.sms.templates.delete'));
        Navigation::active('settings/sms/templates');
    }
}
