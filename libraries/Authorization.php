<?php
namespace packages\sms;
use \packages\userpanel\Authorization as UserPanelAuthorization;

class Authorization extends UserPanelAuthorization{
	static function is_accessed($permission, $prefix = 'sms'){
		return parent::is_accessed($permission, $prefix);
	}
	static function haveOrFail($permission, $prefix = 'sms'){
		parent::haveOrFail($permission, $prefix);
	}
}
