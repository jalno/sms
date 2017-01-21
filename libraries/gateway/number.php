<?php
namespace packages\sms\gateway;
use \packages\base\db\dbObject;
use \packages\base\options;
class number extends dbObject{
	const active = 1;
	const deactive = 2;
	protected $dbTable = "sms_gateways_numbers";
	protected $primaryKey = "id";
	protected $dbFields = array(
		'gateway' => array('type' => 'int', 'required' => true),
		'number' => array('type' => 'text', 'required' => true),
        'status' => array('type' => 'int', 'required' => true)
    );
	protected $relations = array(
		'gateway' => array('hasOne', 'packages\\sms\\gateway', 'gateway')
	);
	protected function byNumber($number){
		$this->where("number", $number);
		$obj = $this->getOne();
		return $obj;
	}
}
