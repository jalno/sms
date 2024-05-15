<?php

namespace packages\sms;

use packages\base\Date;
use packages\base\DB\DBObject;
use packages\base\Utility\Safe;
use packages\userpanel\User;

class Get extends DBObject
{
    public const unread = 1;
    public const read = 2;
    protected $dbTable = 'sms_get';
    protected $primaryKey = 'id';
    protected $dbFields = [
        'receive_at' => ['type' => 'int', 'required' => true],
        'sender_number' => ['type' => 'text', 'required' => true],
        'sender_user' => ['type' => 'int'],
        'receiver_number' => ['type' => 'int', 'required' => true],
        'text' => ['type' => 'text', 'required' => true],
        'status' => ['type' => 'int', 'required' => true],
    ];
    protected $relations = [
        'sender_user' => ['hasOne', 'packages\\userpanel\\user', 'sender_user'],
        'receiver_number' => ['hasOne', 'packages\\sms\\gateway\\number', 'receiver_number'],
    ];

    public function preLoad($data)
    {
        $data['sender_number'] = Safe::cellphone_ir($data['sender_number']);
        if (!isset($data['receive_at'])) {
            $data['receive_at'] = Date::time();
        }
        if (!isset($data['sender_user'])) {
            $user = new User();
            if ($user = $user->where('cellphone', $data['sender_number'])->getOne()) {
                $data['sender_user'] = $user->id;
            }
        }
        if (!isset($data['status'])) {
            $data['status'] = self::unread;
        }

        return $data;
    }
}
