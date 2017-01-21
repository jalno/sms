<?php
namespace packages\sms;
use \packages\base\db\dbObject;
use \packages\base\date;
use \packages\base\utility\safe;
use \packages\userpanel\user;
class get extends dbObject{
	const unread = 1;
	const read = 2;
	protected $dbTable = "sms_get";
	protected $primaryKey = "id";
	protected $dbFields = array(
        'receive_at' => array('type' => 'int', 'required' => true),
        'sender_number' => array('type' => 'text', 'required' => true),
        'sender_user' => array('type' => 'int'),
        'receiver_number' => array('type' => 'int', 'required' => true),
		'text' => array('type' => 'text', 'required' => true),
		'status' => array('type' => 'int', 'required' => true)
    );
	protected $relations = array(
		'sender_user' => array('hasOne', 'packages\\userpanel\\user', 'sender_user'),
		'receiver_number' => array('hasOne', 'packages\\sms\\gateway\\number', 'receiver_number')
	);
	public function preLoad($data){
		$data['sender_number'] = safe::cellphone_ir($data['sender_number']);
		if(!isset($data['receive_at'])){
			$data['receive_at'] = date::time();
		}
		if(!isset($data['sender_user'])){
			$user = new user();
			if($user = $user->where("cellphone", $data['sender_number'])->getOne()){
				$data['sender_user'] = $user->id;
			}
		}
		if(!isset($data['status'])){
			$data['status'] = self::unread;
		}
		return $data;
	}
}
