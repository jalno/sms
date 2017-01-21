<?php
namespace packages\sms;
use \packages\base\db\dbObject;
class template extends dbObject{
	protected $dbTable = "sms_templates";
	protected $primaryKey = "id";
	protected $dbFields = array(
		'name' => array('type' => 'text', 'required' => true),
		'lang' => array('type' => 'text', 'required' => true),
        'text' => array('type' => 'text', 'required' => true)
    );
}
