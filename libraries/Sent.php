<?php

namespace packages\sms;

use packages\base\DB\DBObject;
use packages\sms\Sent\Param;
use packages\userpanel\User;

/**
 * @property int                        $send_at
 * @property GateWay\Number             $sender_number
 * @property User                       $sender_user
 * @property string                     $receiver_number
 * @property User                       $receiver_user
 * @property string                     $text
 * @property int                        $status
 * @property \packages\sms\sent\Param[] $params
 */
class Sent extends DBObject
{
    public const queued = 1;
    public const sending = 2;
    public const sent = 3;
    public const failed = 4;

    protected $tmparams = [];
    protected $dbTable = 'sms_sent';
    protected $primaryKey = 'id';
    protected $dbFields = [
        'send_at' => ['type' => 'int', 'required' => true],
        'sender_number' => ['type' => 'int', 'required' => true],
        'sender_user' => ['type' => 'int'],
        'receiver_number' => ['type' => 'text', 'required' => true],
        'receiver_user' => ['type' => 'int'],
        'text' => ['type' => 'text', 'required' => true],
        'status' => ['type' => 'int', 'required' => true],
    ];
    protected $relations = [
        'sender_number' => ['hasOne', GateWay\Number::class, 'sender_number'],
        'sender_user' => ['hasOne', User::class, 'sender_user'],
        'receiver_user' => ['hasOne', User::class, 'receiver_user'],
        'params' => ['hasMany', Param::class, 'sms'],
    ];

    private function processData($data)
    {
        $newdata = [];
        if (is_array($data)) {
            if (isset($data['params'])) {
                foreach ($data['params'] as $name => $value) {
                    $this->tmparams[$name] = new Param([
                        'name' => $name,
                        'value' => $value,
                    ]);
                }
                unset($data['params']);
            }
            $newdata = $data;
        }

        return $newdata;
    }

    public function preLoad($data)
    {
        if (!isset($data['send_at'])) {
            $data['send_at'] = time();
        }

        return $data;
    }

    public function param($name)
    {
        if (!$this->id) {
            return isset($this->tmparams[$name]) ? $this->tmparams[$name]->value : null;
        } else {
            foreach ($this->params as $param) {
                if ($param->name == $name) {
                    return $param->value;
                }
            }

            return false;
        }
    }

    public function setParam($name, $value)
    {
        $param = false;
        foreach ($this->params as $p) {
            if ($p->name == $name) {
                $param = $p;
                break;
            }
        }
        if (!$param) {
            $param = new Param([
                'name' => $name,
                'value' => $value,
            ]);
        } else {
            $param->value = $value;
        }

        if (!$this->id or $this->isNew) {
            $this->tmparams[$name] = $param;
        } else {
            $param->sms = $this->id;

            return $param->save();
        }
    }

    public function save($data = null)
    {
        if ($return = parent::save($data)) {
            foreach ($this->tmparams as $param) {
                $param->sms = $this->id;
                $param->save();
            }
            $this->tmparams = [];
        }

        return $return;
    }

    public function send()
    {
        $this->status = self::sending;
        $this->save();
        try {
            $status = $this->sender_number->gateway->send($this);
            if (in_array($status, [self::sent, self::failed])) {
                $this->status = $status;
            } else {
                $this->status = self::failed;
            }
        } catch (\Exception $e) {
            $this->status = self::failed;
        }
        $this->save();

        return self::sent == $this->status;
    }
}
