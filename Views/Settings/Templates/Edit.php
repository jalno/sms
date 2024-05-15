<?php

namespace packages\sms\Views\Settings\Templates;

use packages\sms\Template;
use packages\userpanel\Views\Form;

class Edit extends Form
{
    public function setTemplates($templates)
    {
        $this->setData($templates, 'templates');
    }

    protected function getTemplates()
    {
        return $this->getData('templates');
    }

    public function setTemplate(Template $template)
    {
        $this->setData($template, 'template');
        $this->setDataForm($template->toArray());
    }

    protected function getTemplate()
    {
        return $this->getData('template');
    }
}
