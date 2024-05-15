<?php

namespace themes\clipone\Views\SMS\Settings\GateWays;

use packages\base\Translator;
use packages\sms\Views\Settings\GateWays\ListView as GateWaysListView;
use packages\userpanel;
use themes\clipone\Navigation;
use themes\clipone\Navigation\MenuItem;
use themes\clipone\Views\FormTrait;
use themes\clipone\Views\ListTrait;
use themes\clipone\ViewTrait;

class ListView extends GateWaysListView
{
    use ViewTrait;
    use ListTrait;
    use FormTrait;
    private $categories;

    public function __beforeLoad()
    {
        $this->setTitle(Translator::trans('settings.sms.gateways'));
        Navigation::active('settings/sms/gateways');
        $this->setButtons();
        $this->addAssets();
    }

    private function addAssets()
    {
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
            $gateways = new MenuItem('gateways');
            $gateways->setTitle(Translator::trans('settings.sms.gateways'));
            $gateways->setURL(userpanel\url('settings/sms/gateways'));
            $gateways->setIcon('fa fa-rss');
            $sms->addItem($gateways);
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
