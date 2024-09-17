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
        $this->setTitle(t('settings.sms.gateways'));
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

    public static function onSourceLoad()
    {
        parent::onSourceLoad();
        if (parent::$navigation) {
            $settings = Navigation::getByName('settings');
            if (!$sms = Navigation::getByName('settings/sms')) {
                $sms = new MenuItem('sms');
                $sms->setTitle(t('settings.sms'));
                $sms->setIcon('fa fa-envelope');
                if ($settings) {
                    $settings->addItem($sms);
                }
            }
            $gateways = new MenuItem('gateways');
            $gateways->setTitle(t('settings.sms.gateways'));
            $gateways->setURL(userpanel\url('settings/sms/gateways'));
            $gateways->setIcon('fa fa-rss');
            $sms->addItem($gateways);
        }
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
