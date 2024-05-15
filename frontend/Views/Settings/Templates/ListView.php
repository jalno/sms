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
        $this->setTitle(Translator::trans('settings.sms.templates'));
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
                'title' => Translator::trans('sms.template.status.active'),
                'value' => Template::active,
            ],
            [
                'title' => Translator::trans('sms.template.status.deactive'),
                'value' => Template::deactive,
            ],
        ];

        return $options;
    }

    public function getComparisonsForSelect()
    {
        return [
            [
                'title' => Translator::trans('search.comparison.contains'),
                'value' => 'contains',
            ],
            [
                'title' => Translator::trans('search.comparison.equals'),
                'value' => 'equals',
            ],
            [
                'title' => Translator::trans('search.comparison.startswith'),
                'value' => 'startswith',
            ],
        ];
    }

    public static function onSourceLoad()
    {
        parent::onSourceLoad();
        if (parent::$navigation) {
            $settings = Navigation::getByName('settings');
            if (!$sms = Navigation::getByName('settings/sms')) {
                $sms = new MenuItem('sms');
                $sms->setTitle(Translator::trans('settings.sms'));
                $sms->setIcon('fa fa-envelope');
                if ($settings) {
                    $settings->addItem($sms);
                }
            }
            $templates = new MenuItem('templates');
            $templates->setTitle(Translator::trans('settings.sms.templates'));
            $templates->setURL(userpanel\url('settings/sms/templates'));
            $templates->setIcon('fa fa-file-text-o');
            $sms->addItem($templates);
        }
    }

    public function setButtons()
    {
        $this->setButton('edit', $this->canEdit, [
            'title' => Translator::trans('edit'),
            'icon' => 'fa fa-edit',
            'classes' => ['btn', 'btn-xs', 'btn-warning'],
        ]);
        $this->setButton('delete', $this->canDel, [
            'title' => Translator::trans('delete'),
            'icon' => 'fa fa-times',
            'classes' => ['btn', 'btn-xs', 'btn-bricky'],
        ]);
    }
}
