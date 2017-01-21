<?php
namespace packages\sms\listeners\settings;
use \packages\userpanel\usertype\permissions;
class usertype{
	public function permissions_list(){
		$permissions = array(
			'sent_list',
			'sent_list_anonymous',
			'get_list',
			'get_list_anonymous',
			'send',
			"settings_gateways_list",
			"settings_gateways_add",
			"settings_gateways_edit",
			"settings_gateways_delete",
		);
		foreach($permissions as $permission){
			permissions::add('sms_'.$permission);
		}
	}
}
