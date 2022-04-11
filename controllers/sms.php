<?php
namespace packages\sms\controllers;
use \packages\base;
use \packages\base\http;
use \packages\base\db;
use \packages\base\db\parenthesis;
use \packages\base\views\FormError;
use \packages\base\view\error;
use \packages\base\inputValidation;
use \packages\base\utility\safe;

use \packages\userpanel;
use \packages\userpanel\user;
use \packages\userpanel\date;

use \packages\sms\view;
use \packages\sms\authentication;
use \packages\sms\controller;
use \packages\sms\authorization;
use \packages\sms\sent;
use \packages\sms\get;
use \packages\sms\gateway;
use \packages\sms\gateway\number;

use \packages\sms\api;

class sms extends controller{
	protected $authentication = true;
	public function sent(){
		authorization::haveOrFail('sent_list');
		$view = view::byName("\\packages\\sms\\views\\sent\\listview");
		$types = authorization::childrenTypes();
		$sent_list_anonymous = authorization::is_accessed('sent_list_anonymous');
		$inputsRules = array(
			'id' => array(
				'type' => 'number',
				'optional' => true,
				'empty' => true
			),
			'sender_user' => array(
				'type' => 'number',
				'optional' =>true,
				'empty' => true
			),
			'sender_number' => array(
				'type' => 'number',
				'optional' =>true,
				'empty' => true
			),
			'receiver_user' => array(
				'type' => 'number',
				'optional' =>true,
				'empty' => true
			),
			'receiver_number' => array(
				'type' => 'number',
				'optional' =>true,
				'empty' => true
			),
			'text' => array(
				'type' => 'string',
				'optional' =>true,
				'empty' => true
			),
			'status' => array(
				'type' => 'number',
				'optional' =>true,
				'empty' => true
			),
			'word' => array(
				'type' => 'string',
				'optional' => true,
				'empty' => true
			),
			'comparison' => array(
				'values' => array('equals', 'startswith', 'contains'),
				'default' => 'contains',
				'optional' => true
			)
		);
		$this->response->setStatus(true);
		try{
			$inputs = $this->checkinputs($inputsRules);
			if(isset($inputs['status']) and $inputs['status'] != 0){
				if(!in_array($inputs['status'], array(sent::queued, sent::sending, sent::sent,sent::failed))){
					throw new inputValidation("status");
				}
			}
			foreach(array('sender_user', 'receiver_user') as $field){
				if(isset($inputs[$field]) and $inputs[$field] != 0){
					$user = user::byId($inputs[$field]);
					if(!$user){
						throw new inputValidation($field);
					}
					$inputs[$field] = $user->id;
				}
			}

			foreach(array('id', 'sender_user', 'receiver_user', 'sender_number', 'receiver_number', 'text', 'status') as $item){
				if(isset($inputs[$item]) and $inputs[$item]){
					$comparison = $inputs['comparison'];
					if(in_array($item, array('id', 'status', 'sender_user', 'receiver_user'))){
						$comparison = 'equals';
					}
					db::where("sms_sent.".$item, $inputs[$item], $comparison);
				}
			}
			if(isset($inputs['word']) and $inputs['word']){
				$parenthesis = new parenthesis();
				foreach(array('sender_number', 'receiver_number', 'text') as $item){
					if(!isset($inputs[$item]) or !$inputs[$item]){
						$parenthesis->where("sms_sent.".$item,$inputs['word'], $inputs['comparison'], 'OR');
					}
				}
				db::where($parenthesis);
			}
			if($sent_list_anonymous){
				db::join("userpanel_users", "userpanel_users.id=sms_sent.receiver_user", "left");
				$parenthesis = new parenthesis();
				$parenthesis->where("userpanel_users.type",  $types, 'in');
				$parenthesis->where("sms_sent.receiver_user", null, 'is','or');
				db::where($parenthesis);
			}else{
				db::join("userpanel_users", "userpanel_users.id=sms_sent.receiver_user", "inner");
				if($types){
					db::where("userpanel_users.type", $types, 'in');
				}else{
					db::where("userpanel_users.id", authentication::getID());
				}
			}
			db::orderBy('sms_sent.id', ' DESC');
			db::pageLimit($this->items_per_page);
			$items = db::paginate('sms_sent', $this->page, array("sms_sent.*"));
			$view->setPaginate($this->page, db::totalCount(), $this->items_per_page);
			$sents = array();
			foreach($items  as $item){
				$sents[] = new sent($item);
			}
			$view->setDataList($sents);
		}catch(inputValidation $error){
			$view->setFormError(FormError::fromException($error));
			$this->response->setStatus(false);
		}
		$view->setDataForm($this->inputsvalue($inputs));

		$this->response->setStatus(true);
		$this->response->setView($view);
		return $this->response;
	}
	public function get($name){
		authorization::haveOrFail('get_list');
		$view = view::byName("\\packages\\sms\\views\\get\\listview");
		$types = authorization::childrenTypes();
		$get_list_anonymous = authorization::is_accessed('get_list_anonymous');
		$inputsRules = array(
			'id' => array(
				'type' => 'number',
				'optional' => true,
				'empty' => true
			),
			'sender_user' => array(
				'type' => 'number',
				'optional' =>true,
				'empty' => true
			),
			'sender_number' => array(
				'type' => 'number',
				'optional' =>true,
				'empty' => true
			),
			'receiver_number' => array(
				'type' => 'number',
				'optional' =>true,
				'empty' => true
			),
			'text' => array(
				'type' => 'string',
				'optional' =>true,
				'empty' => true
			),
			'status' => array(
				'type' => 'number',
				'optional' =>true,
				'empty' => true
			),
			'word' => array(
				'type' => 'string',
				'optional' => true,
				'empty' => true
			),
			'comparison' => array(
				'values' => array('equals', 'startswith', 'contains'),
				'default' => 'contains',
				'optional' => true
			)
		);
		$this->response->setStatus(true);
		try{
			$inputs = $this->checkinputs($inputsRules);
			if(isset($inputs['status']) and $inputs['status'] != 0){
				if(!in_array($inputs['status'], array(get::unread, get::read))){
					throw new inputValidation("status");
				}
			}
			if(isset($inputs['sender_user']) and $inputs['sender_user'] != 0){
				$user = user::byId($inputs['sender_user']);
				if(!$user){
					throw new inputValidation('sender_user');
				}
				$inputs['sender_user'] = $user->id;
			}

			foreach(array('id', 'sender_user', 'sender_number', 'receiver_number', 'text', 'status') as $item){
				if(isset($inputs[$item]) and $inputs[$item]){
					$comparison = $inputs['comparison'];
					if(in_array($item, array('id', 'status', 'sender_user'))){
						$comparison = 'equals';
					}
					db::where("sms_get.".$item, $inputs[$item], $comparison);
				}
			}
			if(isset($inputs['word']) and $inputs['word']){
				$parenthesis = new parenthesis();
				foreach(array('sender_number', 'receiver_number', 'text') as $item){
					if(!isset($inputs[$item]) or !$inputs[$item]){
						$parenthesis->where("sms_get.".$item,$inputs['word'], $inputs['comparison'], 'OR');
					}
				}
				db::where($parenthesis);
			}
			if($get_list_anonymous){
				db::join("userpanel_users", "userpanel_users.id=sms_get.sender_user", "left");
				$parenthesis = new parenthesis();
				$parenthesis->where("userpanel_users.type",  $types, 'in');
				$parenthesis->where("sms_get.sender_user", null, 'is','or');
				db::where($parenthesis);
			}else{
				db::join("userpanel_users", "userpanel_users.id=sms_get.sender_user", "inner");
				if($types){
					db::where("userpanel_users.type", $types, 'in');
				}else{
					db::where("userpanel_users.id", authentication::getID());
				}
			}
			db::orderBy('sms_get.id', ' DESC');
			db::pageLimit($this->items_per_page);
			$items = db::paginate('sms_get', $this->page, array("sms_get.*"));
			$view->setPaginate($this->page, db::totalCount(), $this->items_per_page);
			$gets = array();
			foreach($items  as $item){
				$gets[] = new get($item);
			}
			$view->setDataList($gets);
		}catch(inputValidation $error){
			$view->setFormError(FormError::fromException($error));
			$this->response->setStatus(false);
		}
		$view->setDataForm($this->inputsvalue($inputs));

		$this->response->setStatus(true);
		$this->response->setView($view);
		return $this->response;
	}
	public function send(){
		$view = view::byName("\\packages\\sms\\views\\send");
		authorization::haveOrFail('send');
		db::join("sms_gateways", "sms_gateways_numbers.gateway=sms_gateways.id", "inner");
		db::where("sms_gateways.status", gateway::active);
		db::where("sms_gateways_numbers.status", number::active);
		$numbersData = db::get("sms_gateways_numbers", null, "sms_gateways_numbers.*");
		$numbers = array();
		foreach($numbersData as $data){
			$numbers[] = new number($data);
		}

		$view->setNumbers($numbers);
		if(http::is_post()){

			$this->response->setStatus(false);
			$inputsRules = array(
				'to' => array(
					'type' => 'cellphone'
				),
				'from' => array(
					'type' => 'number',
					'optional' => true
				),
				'text' => array(
					'type' => 'string',
					'multiLine' => true,
				)
			);
			try {
				$inputs = $this->checkinputs($inputsRules);
				$inputs['text'] = str_replace("\r\n", "\n", $inputs['text']); // this is for save charachters

				if(array_key_exists('from',$inputs)){
					if(!$inputs['from'] = number::byId($inputs['from'])){
						throw new inputValidation("from");
					}
					if($inputs['from']->status != number::active or $inputs['from']->gateway->status != gateway::active){
						throw new inputValidation('from');
					}
				}
				$sms = new api;
				$sms->to($inputs['to']);
				$sms->fromUser(authentication::getUser());
				if(array_key_exists('from',$inputs)){
					$sms->fromNumber($inputs['from']);
				}
				$sms->now();
				if($sms->send($inputs['text']) != sent::sent){
					throw new sendException();
				}
				$this->response->setStatus(true);
				$this->response->Go(userpanel\url('sms/sent'));
			}catch(inputValidation $error){
				$view->setFormError(FormError::fromException($error));
			}catch(sendException $error){
				$error = new error();
				$error->setCode('sms.send');
				$view->addError($error);
			}
			$view->setDataForm($this->inputsvalue($inputsRules));
		}else{
			$this->response->setStatus(true);
			if(isset(http::$request['get']['to'])){
				if(safe::is_cellphone_ir(http::$request['get']['to'])){
					$view->setDataForm(safe::cellphone_ir(http::$request['get']['to']), 'to');
				}
			}
		}
		$this->response->setView($view);
		return $this->response;
	}

}
class sendException extends \Exception{}
