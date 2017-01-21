<?php
namespace packages\sms\sent;
use \packages\base\db\dbObject;
class param extends dbObject{
	protected $dbTable = "sms_sent_params";
	protected $primaryKey = "id";
	protected $dbFields = array(
        'sms' => array('type' => 'int', 'required' => true),
        'name' => array('type' => 'text', 'required' => true),
        'value' => array('type' => 'text', 'required' => true)
    );
}
