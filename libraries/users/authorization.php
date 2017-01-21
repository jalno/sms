<?php
namespace packages\sms;
use \packages\userpanel\authorization as UserPanelAuthorization;

class authorization extends UserPanelAuthorization{
	static function is_accessed($permission, $prefix = 'sms'){
		return parent::is_accessed($permission, $prefix);
	}
	static function haveOrFail($permission, $prefix = 'sms'){
		parent::haveOrFail($permission, $prefix);
	}
}
