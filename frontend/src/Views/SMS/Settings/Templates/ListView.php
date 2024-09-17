<?php

namespace themes\clipone\Views\SMS\Settings\Templates;

use packages\base\Translator;
use packages\sms\Template;
use packages\sms\Views\Settings\Templates\ListView as TemplatesListView;
use packages\userpanel;
use themes\clipone\Navigation;
use themes\clipone\Navigation\MenuItem;
use themes\clipone\Views\FormTrait;
use themes\clipone\Views\ListTrait;
use themes\clipone\ViewTrait;

class ListView extends TemplatesListView
{
    use ViewTrait;
    use ListTrait;
    use FormTrait;
    private $categories;

    public function __beforeLoad()
    {
        $this->setTitle(t('settings.sms.templates'));
        Navigation::active('settings/sms/templates');
        $this->setButtons();
        $this->addAssets();
    }

    private function addAssets()
    {
    }

    public function getTemplateStatusForSelect()
    {
        $options = [
            [
                'title' => '',
                'value' => '',
            ],
            [
                'title' => t('sms.template.status.active'),
                'value' => Template::active,
            ],
            [
                'title' => t('sms.template.status.deactive'),
                'value' => Template::deactive,
            ],
        ];

        return $options;
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

    public function setButtons()
    {
        $this->setButton('edit', $this->canEdit, [
            'title' => t('edit'),
            'icon' => 'fa fa-edit',
            'classes' => ['btn', 'btn-xs', 'btn-warning'],
        ]);
        $this->setButton('delete', $this->canDel, [
            'title' => t('delete'),
            'icon' => 'fa fa-times',
            'classes' => ['btn', 'btn-xs', 'btn-bricky'],
        ]);
    }
}
