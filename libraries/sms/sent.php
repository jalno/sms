<?php

namespace packages\sms;

use packages\base\db\DBObject;
use packages\sms\Gateway;
use packages\sms\sent\Param;
use packages\userpanel\User;

/**
 * @property int $send_at
 * @property \packages\sms\Gateway\Number $sender_number
 * @property \packages\userpanel\User $sender_user
 * @property string $receiver_number
 * @property \packages\userpanel\User $receiver_user
 * @property string $text
 * @property int $status
 * @property \packages\sms\sent\Param[] $params
 */
class Sent extends DBObject {

	const queued = 1;
	const sending = 2;
	const sent = 3;
	const failed = 4;

	protected $tmparams = array();
	protected $dbTable = "sms_sent";
	protected $primaryKey = "id";
	protected $dbFields = array(
        'send_at' => array('type' => 'int', 'required' => true),
        'sender_number' => array('type' => 'int', 'required' => true),
        'sender_user' => array('type' => 'int'),
        'receiver_number' => array('type' => 'text', 'required' => true),
        'receiver_user' => array('type' => 'int'),
		'text' => array('type' => 'text', 'required' => true),
		'status' => array('type' => 'int', 'required' => true)
    );
	protected $relations = array(
		'sender_number' => array('hasOne', Gateway\Number::class, 'sender_number'),
		'sender_user' => array('hasOne', User::class, 'sender_user'),
		'receiver_user' => array('hasOne', User::class, 'receiver_user'),
		'params' => array('hasMany', Param::class, 'sms'),
	);
	private function processData($data){
		$newdata = array();
		if(is_array($data)){
			if(isset($data['params'])){
				foreach($data['params'] as $name => $value){
					$this->tmparams[$name] = new param(array(
						'name' => $name,
						'value' => $value
					));
				}
				unset($data['params']);
			}
			$newdata = $data;
		}
		return $newdata;
	}
	public function preLoad($data){
		if(!isset($data['send_at'])){
			$data['send_at'] = time();
		}
		return $data;
	}
	public function param($name){
		if(!$this->id){
			return(isset($this->tmparams[$name]) ? $this->tmparams[$name]->value : null);
		}else{
			foreach($this->params as $param){
				if($param->name == $name){
					return $param->value;
				}
			}
			return false;
		}
	}
	public function setParam($name, $value){
		$param = false;
		foreach($this->params as $p){
			if($p->name == $name){
				$param = $p;
				break;
			}
		}
		if(!$param){
			$param = new param(array(
				'name' => $name,
				'value' => $value
			));
		}else{
			$param->value = $value;
		}

		if(!$this->id or $this->isNew){
			$this->tmparams[$name] = $param;
		}else{
			$param->sms = $this->id;
			return $param->save();
		}
	}
	public function save($data = null) {
		if($return = parent::save($data)){
			foreach($this->tmparams as $param){
				$param->sms = $this->id;
				$param->save();
			}
			$this->tmparams = array();
		}
		return $return;
	}
	public function send(){
		$this->status = self::sending;
		$this->save();
		try {
			$status = $this->sender_number->gateway->send($this);
			if (in_array($status, array(self::sent, self::failed))) {
				$this->status = $status;
			} else {
				$this->status = self::failed;
			}
		} catch (\Exception $e) {
			$this->status = self::failed;
		}
		$this->save();
		return $this->status == self::sent;
	}
}
