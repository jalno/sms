<?php
namespace packages\sms\controllers\settings;
use \packages\base;
use \packages\base\frontend\theme;
use \packages\base\NotFound;
use \packages\base\http;
use \packages\base\db;
use \packages\base\db\parenthesis;
use \packages\base\db\duplicateRecord;
use \packages\base\views\FormError;
use \packages\base\view\error;
use \packages\base\inputValidation;
use \packages\base\events;
use \packages\base\options;

use \packages\userpanel;
use \packages\userpanel\user;
use \packages\userpanel\date;

use \packages\sms\view;
use \packages\sms\authentication;
use \packages\sms\controller;
use \packages\sms\authorization;
use \packages\sms\gateway;
use \packages\sms\gateway\number;
use \packages\sms\events\gateways as gatewaysEvent;

use \packages\sms\api;

class gateways extends controller{
	protected $authentication = true;
	public function listgateways(){
		authorization::haveOrFail('settings_gateways_list');
		$view = view::byName("\\packages\\sms\\views\\settings\\gateways\\listview");
		$gateways = new gatewaysEvent();
		events::trigger($gateways);
		$gateway = new gateway();
		$inputsRules = array(
			'id' => array(
				'type' => 'number',
				'optional' => true,
				'empty' => true
			),
			'title' => array(
				'type' => 'string',
				'optional' =>true,
				'empty' => true
			),
			'gateway' => array(
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
				if(!in_array($inputs['status'], array(gateway::active, gateway::deactive))){
					throw new inputValidation("status");
				}
			}
			if(isset($inputs['gateway']) and $inputs['gateway']){
				if(!in_array($inputs['gateway'], $gateways->getGatewayNames())){
					throw new inputValidation("gateway");
				}
			}

			foreach(array('id', 'title', 'gateway', 'status') as $item){
				if(isset($inputs[$item]) and $inputs[$item]){
					$comparison = $inputs['comparison'];
					if(in_array($item, array('id','gateway', 'status'))){
						$comparison = 'equals';
						if($item == 'gateway'){
							$inputs[$item] = $gateways->getByName($inputs[$item]);
						}
					}
					$gateway->where($item, $inputs[$item], $comparison);
				}
			}
			if(isset($inputs['word']) and $inputs['word']){
				$parenthesis = new parenthesis();
				foreach(array('title') as $item){
					if(!isset($inputs[$item]) or !$inputs[$item]){
						$parenthesis->where("sms_gateways.".$item,$inputs['word'], $inputs['comparison'], 'OR');
					}
				}
				$gateway->where($parenthesis);
			}
		}catch(inputValidation $error){
			$view->setFormError(FormError::fromException($error));
			$this->response->setStatus(false);
		}
		$view->setDataForm($this->inputsvalue($inputsRules));
		$gateway->orderBy('id', 'ASC');
		$gateway->pageLimit = $this->items_per_page;
		$items = $gateway->paginate($this->page);
		$view->setPaginate($this->page, $gateway->totalCount, $this->items_per_page);
		$view->setDataList($items);
		$view->setGateways($gateways);
		$this->response->setView($view);
		return $this->response;
	}
	public function add(){
		authorization::haveOrFail('settings_gateways_add');
		$view = view::byName("\\packages\\sms\\views\\settings\\gateways\\add");
		$gateways = new gatewaysEvent();
		events::trigger($gateways);
		$view->setGateways($gateways);
		if(http::is_post()){
			$inputsRules = array(
				'title' => array(
					'type' => 'string'
				),
				'gateway' => array(
					'type' => 'string',
					'values' => $gateways->getGatewayNames()
				),
				'status' => array(
					'type' => 'number',
					'values' => array(gateway::active, gateway::deactive)
				),
				'numbers' => array()
			);
			$this->response->setStatus(true);
			try{
				$inputs = $this->checkinputs($inputsRules);
				$gateway =  $gateways->getByName($inputs['gateway']);
				if($GRules = $gateway->getInputs()){
					$GRules = $inputsRules = array_merge($inputsRules, $GRules);
					$ginputs = $this->checkinputs($GRules);
				}
				if(isset($inputs['numbers'])){
					if(is_array($inputs['numbers'])){
						foreach($inputs['numbers'] as $key => $data){
							if(isset($data['number']) and preg_match("/^\d+$/", $data['number'])){
								if(isset($data['status']) and in_array($data['status'], array(number::active, number::deactive))){
									if(number::byNumber($data['number'])){
										throw new duplicateRecord("numbers[{$key}][number]");
									}
								}else{
									throw new inputValidation("numbers[{$key}][status]");
								}
							}else{
								throw new inputValidation("numbers[{$key}][number]");
							}
						}
					}else{
						throw new inputValidation("numbers");
					}
				}
				if($GRules = $gateway->getInputs()){
					$gateway->callController($ginputs);
				}
				$gatewayObj = new gateway();
				$gatewayObj->title = $inputs['title'];
				$gatewayObj->handler = $gateway->getHandler();
				$gatewayObj->status = $inputs['status'];
				foreach($gateway->getInputs() as $input){
					if(isset($ginputs[$input['name']])){
						$gatewayObj->setParam($input['name'],$ginputs[$input['name']]);
					}
				}
				$gatewayObj->save();
				if(isset($inputs['numbers'])){
					foreach($inputs['numbers'] as $data){
						$number = new number();
						$number->gateway = $gatewayObj->id;
						$number->number = $data['number'];
						$number->status = $data['status'];
						$number->save();
						if(isset($data['primary']) and $data['primary']){
							options::save('packages.sms.defaultNumber', $number->id);
						}
					}
				}
				$this->response->setStatus(true);
				$this->response->Go(userpanel\url('settings/sms/gateways/edit/'.$gatewayObj->id));
			}catch(inputValidation $error){
				$view->setFormError(FormError::fromException($error));
				$this->response->setStatus(false);
			}catch(duplicateRecord $error){
				$view->setFormError(FormError::fromException($error));
				$this->response->setStatus(false);
			}
			$view->setDataForm($this->inputsvalue($inputsRules));
		}else{
			$this->response->setStatus(true);
		}
		$this->response->setView($view);
		return $this->response;
	}
	public function delete($data){
		authorization::haveOrFail('settings_gateways_delete');
		if(!$gateway = gateway::byID($data['gateway'])){
			throw new NotFound;
		}
		$view = view::byName("\\packages\\sms\\views\\settings\\gateways\\delete");
		$view->setGateway($gateway);
		if(http::is_post()){
			$gateway->delete();

			$this->response->setStatus(true);
			$this->response->Go(userpanel\url('settings/sms/gateways'));
		}else{
			$this->response->setStatus(true);
		}
		$this->response->setView($view);
		return $this->response;
	}
	public function edit($data){
		authorization::haveOrFail('settings_gateways_edit');
		if(!$gatewayObj = gateway::byID($data['gateway'])){
			throw new NotFound;
		}
		$view = view::byName("\\packages\\sms\\views\\settings\\gateways\\edit");
		$gateways = new gatewaysEvent();
		events::trigger($gateways);
		$view->setGateways($gateways->get());
		$view->setGateway($gatewayObj);
		if(http::is_post()){
			$inputsRules = array(
				'title' => array(
					'type' => 'string'
				),
				'gateway' => array(
					'type' => 'string',
					'values' => $gateways->getGatewayNames()
				),
				'status' => array(
					'type' => 'number',
					'values' => array(gateway::active, gateway::deactive)
				),
				'numbers' => array()
			);
			$this->response->setStatus(true);
			try{
				$inputs = $this->checkinputs($inputsRules);
				$gateway =  $gateways->getByName($inputs['gateway']);
				if($GRules = $gateway->getInputs()){
					$GRules = $inputsRules = array_merge($inputsRules, $GRules);
					$ginputs = $this->checkinputs($GRules);
				}
				if(isset($inputs['numbers'])){
					if(is_array($inputs['numbers'])){
						foreach($inputs['numbers'] as $key => $data){
							if(isset($data['number']) and preg_match("/^\d+$/", $data['number'])){
								if(isset($data['status']) and in_array($data['status'], array(number::active, number::deactive))){
									if(number::where("gateway", $gatewayObj->id, '!=')->byNumber($data['number'])){
										throw new duplicateRecord("numbers[{$key}][number]");
									}
								}else{
									throw new inputValidation("numbers[{$key}][status]");
								}
							}else{
								throw new inputValidation("numbers[{$key}][number]");
							}
						}
					}else{
						throw new inputValidation("numbers");
					}
				}
				if($GRules = $gateway->getInputs()){
					$gateway->callController($ginputs);
				}
				$gatewayObj->title = $inputs['title'];
				$gatewayObj->handler = $gateway->getHandler();
				$gatewayObj->status = $inputs['status'];
				foreach($gateway->getInputs() as $input){
					if(isset($ginputs[$input['name']])){
						$gatewayObj->setParam($input['name'],$ginputs[$input['name']]);
					}
				}
				$gatewayObj->save();
				if(isset($inputs['numbers'])){
					foreach($inputs['numbers'] as $data){
						$numberObj = null;
						foreach($gatewayObj->numbers as $number){
							if($number->number == $data['number']){
								$numberObj = $number;
								break;
							}
						}
						if(!$numberObj){
							$numberObj = new number();
							$numberObj->gateway = $gatewayObj->id;
						}
						$numberObj->number = $data['number'];
						$numberObj->status = $data['status'];
						$numberObj->save();
						if(isset($data['primary']) and $data['primary']){
							options::save('packages.sms.defaultNumber', $number->id);
						}
					}
					foreach($gatewayObj->numbers as $number){
						$found = false;
						foreach($inputs['numbers'] as $data){
							if($number->number == $data['number']){
								$found = true;
								break;
							}
						}
						if(!$found){
							$number->delete();
						}
					}
				}
				$this->response->setStatus(true);
			}catch(inputValidation $error){
				$view->setFormError(FormError::fromException($error));
				$this->response->setStatus(false);
			}catch(duplicateRecord $error){
				$view->setFormError(FormError::fromException($error));
				$this->response->setStatus(false);
			}
			$view->setDataForm($this->inputsvalue($inputsRules));
		}else{
			$this->response->setStatus(true);
		}
		$this->response->setView($view);
		return $this->response;
	}
}
