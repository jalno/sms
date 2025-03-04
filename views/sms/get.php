<?php
namespace packages\sms\views\get;
use \packages\sms\views\listview as list_view;
use \packages\base\views\traits\form as formTrait;
use \packages\sms\authorization;

class listview extends list_view{
	use formTrait;
	protected $canSend;
	static protected $navigation;
	function __construct(){
		$this->canSend = authorization::is_accessed('send');
	}

	public static function onSourceLoad(){
		self::$navigation = authorization::is_accessed('get_list');
	}
}
