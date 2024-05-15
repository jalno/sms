<?php
namespace packages\sms\GateWay;

use packages\base\DB\DBObject;
use packages\base\Options;
use packages\sms\GateWay;

/**
 * @property \packages\sms\GateWay $gateway
 * @property string $number
 * @property int $status
 */
class Number extends DBObject {

	const active = 1;
	const deactive = 2;

	public static function byNumber(string $number): ?self {
		return (new self)->where('number', $number)->getOne();
	}

	protected $dbTable = "sms_gateways_numbers";
	protected $primaryKey = "id";
	protected $dbFields = array(
		'gateway' => array('type' => 'int', 'required' => true),
		'number' => array('type' => 'text', 'required' => true),
		'status' => array('type' => 'int', 'required' => true)
	);
	protected $relations = array(
		'gateway' => array('hasOne', GateWay::class, 'gateway')
	);
}
