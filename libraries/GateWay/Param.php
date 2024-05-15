<?php

namespace packages\sms\GateWay;

use packages\base\DB\DBObject;

class Param extends DBObject
{
    protected $dbTable = 'sms_gateways_params';
    protected $primaryKey = 'id';
    private $hadlerClass;
    protected $dbFields = [
        'gateway' => ['type' => 'int', 'required' => true],
        'name' => ['type' => 'text', 'required' => true],
        'value' => ['type' => 'text'],
    ];
}
