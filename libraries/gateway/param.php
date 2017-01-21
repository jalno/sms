<?php
namespace packages\sms\gateway;
use \packages\base\db\dbObject;
class param extends dbObject{
	protected $dbTable = "sms_gateways_params";
	protected $primaryKey = "id";
	private $hadlerClass;
	protected $dbFields = array(
		'gateway' => array('type' => 'int', 'required' => true),
		'name' => array('type' => 'text', 'required' => true),
        'value' => array('type' => 'text')
    );
}
