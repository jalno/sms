<?php
namespace packages\sms\GateWay;
use \packages\base\DB\DBObject;
class Param extends DBObject{
	protected $dbTable = "sms_gateways_params";
	protected $primaryKey = "id";
	private $hadlerClass;
	protected $dbFields = array(
		'gateway' => array('type' => 'int', 'required' => true),
		'name' => array('type' => 'text', 'required' => true),
        'value' => array('type' => 'text')
    );
}
