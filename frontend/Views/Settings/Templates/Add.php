<?php

namespace themes\clipone\Views\SMS\Settings\Templates;

use packages\base\Translator;
use packages\sms\Template;
use packages\sms\Views\Settings\Templates\Add as AddView;
use packages\userpanel;
use themes\clipone\Breadcrumb;
use themes\clipone\Navigation;
use themes\clipone\Views\FormTrait;
use themes\clipone\ViewTrait;

class Add extends AddView
{
    use ViewTrait;
    use FormTrait;

    public function __beforeLoad()
    {
        $this->setTitle(Translator::trans('settings.sms.templates.add'));
        $this->setNavigation();
        $this->addBodyClass('sms_templates');
        if (!$this->getDataForm('lang')) {
            $this->setDataForm('fa', 'lang');
        }
    }

    private function setNavigation()
    {
        $add = new Navigation\MenuItem('template_add');
        $add->setTitle(Translator::trans('add'));
        $add->setIcon('fa fa-plus');
        $add->setURL(userpanel\url('settings/sms/templates/add'));
        // breadcrumb::addItem($add);
        Navigation::active('settings/sms/templates');
    }

    public function getTemplatesForSelect()
    {
        $options = [];
        foreach ($this->getTemplates() as $template) {
            $title = Translator::trans('sms.template.name.'.$template->name);
            $variables = [];
            foreach ($template->variables as $variable) {
                $description = '';
                $name = explode('->', $variable);
                for ($x = 0; $x != count($name) and !$description; ++$x) {
                    $variable_name = implode('->', array_slice($name, $x));
                    $description = Translator::trans('sms.template.variable.'.$variable_name);
                }
                $variables[] = [
                    'key' => $variable,
                    'description' => (string) $description,
                ];
            }
            $options[] = [
                'value' => $template->name,
                'title' => $title ? $title : $template->name,
                'data' => [
                    'variables' => $variables,
                ],
            ];
        }

        return $options;
    }

    public function getTemplateStatusForSelect()
    {
        $options = [
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

    public function getLanguagesForSelect()
    {
        $options = [];
        foreach (Translator::$allowlangs as $lang) {
            $options[] = [
                'title' => Translator::trans('translations.langs.'.$lang),
                'value' => $lang,
            ];
        }

        return $options;
    }
}
