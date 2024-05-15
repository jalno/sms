<?php
namespace packages\sms\Sent;
use \packages\base\DB\DBObject;
class Param extends DBObject{
	protected $dbTable = "sms_sent_params";
	protected $primaryKey = "id";
	protected $dbFields = array(
        'sms' => array('type' => 'int', 'required' => true),
        'name' => array('type' => 'text', 'required' => true),
        'value' => array('type' => 'text', 'required' => true)
    );
}
