<?php

namespace packages\sms\Sent;

use packages\base\DB\DBObject;

class Param extends DBObject
{
    protected $dbTable = 'sms_sent_params';
    protected $primaryKey = 'id';
    protected $dbFields = [
        'sms' => ['type' => 'int', 'required' => true],
        'name' => ['type' => 'text', 'required' => true],
        'value' => ['type' => 'text', 'required' => true],
    ];
}
