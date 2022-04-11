<?php
namespace packages\sms\views\sent;
use \packages\sms\views\listview as list_view;
use \packages\base\views\traits\form as formTrait;
use \packages\sms\authorization;

class listview extends list_view{
	use formTrait;

	public bool $canSend;

	static protected $navigation;

	function __construct(){
		$this->canSend = authorization::is_accessed('send');
	}

	public static function onSourceLoad(){
		self::$navigation = authorization::is_accessed('sent_list');
	}
}
