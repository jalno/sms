<?php

namespace packages\sms\GateWay;

use packages\base\DB\DBObject;
use packages\sms\GateWay;

/**
 * @property GateWay $gateway
 * @property string  $number
 * @property int     $status
 */
class Number extends DBObject
{
    public const active = 1;
    public const deactive = 2;

    public static function byNumber(string $number): ?self
    {
        return (new self())->where('number', $number)->getOne();
    }

    protected $dbTable = 'sms_gateways_numbers';
    protected $primaryKey = 'id';
    protected $dbFields = [
        'gateway' => ['type' => 'int', 'required' => true],
        'number' => ['type' => 'text', 'required' => true],
        'status' => ['type' => 'int', 'required' => true],
    ];
    protected $relations = [
        'gateway' => ['hasOne', GateWay::class, 'gateway'],
    ];
}
