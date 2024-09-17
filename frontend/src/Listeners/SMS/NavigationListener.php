<?php
namespace themes\clipone\Listeners\SMS;

use packages\sms\Authorization;
use themes\clipone\Navigation;
use themes\clipone\Navigation\MenuItem;
use themes\clipone\Views\Dashboard;

use function packages\userpanel\url;

class NavigationListener
{
	public function initial(): void
	{
		if (Authorization::is_accessed('get_list'))
		{
			$get = new MenuItem('get');
			$get->setTitle(t('sms.get'));
			$get->setURL(url('sms/get'));
			$get->setIcon('clip-download');
			$this->getSMS()->addItem($get);
		}

		if (Authorization::is_accessed('sent_list'))
		{
			$sent = new MenuItem('sent');
			$sent->setTitle(t('sms.sent'));
			$sent->setURL(url('sms/sent'));
			$sent->setIcon('clip-upload');
			$this->getSMS()->addItem($sent);
		}

		if (Authorization::is_accessed('settings_gateways_list')) {
			$gateways = new MenuItem('gateways');
			$gateways->setTitle(t('settings.sms.gateways'));
			$gateways->setURL(url('settings/sms/gateways'));
			$gateways->setIcon('fa fa-rss');
			$this->getSMSSettings()->addItem($gateways);
		}

		if (Authorization::is_accessed('settings_templates_list')) {
			$templates = new MenuItem('templates');
			$templates->setTitle(t('settings.sms.templates'));
			$templates->setURL(url('settings/sms/templates'));
			$templates->setIcon('fa fa-file-text-o');
			$this->getSMSSettings()->addItem($templates);
		}
	}

	protected function getSMS(): MenuItem {
		$sms = Navigation::getByName('sms');
		if (!$sms) {
			$sms = new MenuItem('sms');
			$sms->setTitle(t('smses'));
			$sms->setIcon('fa fa-envelope');
			Navigation::addItem($sms);
		}

		return $sms;
	}

	protected function getSMSSettings(): MenuItem {
		$settings = Dashboard::getSettingsMenu();
		$sms = Navigation::getByName('settings/sms');
		if (!$sms) {
			$sms = new MenuItem('sms');
			$sms->setTitle(t('settings.sms'));
			$sms->setIcon('fa fa-envelope');
			$settings->addItem($sms);
		}

		return $sms;
	}
}