<?php
namespace packages\sms\Controllers\Settings;
use \packages\base;
use \packages\base\Frontend\Theme;
use \packages\base\NotFound;
use \packages\base\HTTP;
use \packages\base\DB;
use \packages\base\DB\Parenthesis;
use \packages\base\DB\DuplicateRecord;
use \packages\base\Views\FormError;
use \packages\base\View\Error;
use \packages\base\InputValidation;
use \packages\base\Events;
use \packages\base\Options;

use \packages\userpanel;
use \packages\userpanel\User;
use \packages\userpanel\Date;

use \packages\sms\View;
use \packages\sms\Authentication;
use \packages\sms\Controller;
use \packages\sms\Authorization;
use \packages\sms\GateWay;
use \packages\sms\GateWay\Number;
use \packages\sms\Events\GateWays as GateWaysEvent;

use \packages\sms\API;

class GateWays extends Controller{
	protected $authentication = true;
	public function listgateways(){
		Authorization::haveOrFail('settings_gateways_list');
		$view = View::byName(\packages\sms\Views\Settings\GateWays\ListView::class);
		$gateways = new GateWaysEvent();
		Events::trigger($gateways);
		$gateway = new GateWay();
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
				if(!in_array($inputs['status'], array(GateWay::active, GateWay::deactive))){
					throw new InputValidation("status");
				}
			}
			if(isset($inputs['gateway']) and $inputs['gateway']){
				if(!in_array($inputs['gateway'], $gateways->getGatewayNames())){
					throw new InputValidation("gateway");
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
				$parenthesis = new Parenthesis();
				foreach(array('title') as $item){
					if(!isset($inputs[$item]) or !$inputs[$item]){
						$parenthesis->where("sms_gateways.".$item,$inputs['word'], $inputs['comparison'], 'OR');
					}
				}
				$gateway->where($parenthesis);
			}
		}catch(InputValidation $error){
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
		Authorization::haveOrFail('settings_gateways_add');
		$view = View::byName("\\packages\\sms\\views\\settings\\gateways\\add");
		$gateways = new GateWaysEvent();
		Events::trigger($gateways);
		$view->setGateways($gateways);
		if(HTTP::is_post()){
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
					'values' => array(GateWay::active, GateWay::deactive)
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
								if(isset($data['status']) and in_array($data['status'], array(Number::active, Number::deactive))){
									if(Number::byNumber($data['number'])){
										throw new DuplicateRecord("numbers[{$key}][number]");
									}
								}else{
									throw new InputValidation("numbers[{$key}][status]");
								}
							}else{
								throw new InputValidation("numbers[{$key}][number]");
							}
						}
					}else{
						throw new InputValidation("numbers");
					}
				}
				if($GRules = $gateway->getInputs()){
					$gateway->callController($ginputs);
				}
				$gatewayObj = new GateWay();
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
						$number = new Number();
						$number->gateway = $gatewayObj->id;
						$number->number = $data['number'];
						$number->status = $data['status'];
						$number->save();
						if(isset($data['primary']) and $data['primary']){
							Options::save('packages.sms.defaultNumber', $number->id);
						}
					}
				}
				$this->response->setStatus(true);
				$this->response->Go(userpanel\url('settings/sms/gateways/edit/'.$gatewayObj->id));
			}catch(InputValidation $error){
				$view->setFormError(FormError::fromException($error));
				$this->response->setStatus(false);
			}catch(DuplicateRecord $error){
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
		Authorization::haveOrFail('settings_gateways_delete');
		$gateway = (new GateWay)->byID($data['gateway']);
		if (!$gateway) {
			throw new NotFound;
		}
		$view = View::byName(\packages\sms\Views\Settings\GateWays\Delete::class);
		$view->setGateway($gateway);
		if(HTTP::is_post()){
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
		Authorization::haveOrFail('settings_gateways_edit');
		$gatewayObj = (new GateWay)->byID($data['gateway']);
		if (!$gatewayObj) {
			throw new NotFound;
		}
		$view = view::byName(\packages\sms\Views\Settings\GateWays\Edit::class);
		$gateways = new GateWaysEvent();
		Events::trigger($gateways);
		$view->setGateways($gateways->get());
		$view->setGateway($gatewayObj);
		if(HTTP::is_post()){
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
					'values' => array(GateWay::active, GateWay::deactive)
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
								if(isset($data['status']) and in_array($data['status'], array(Number::active, Number::deactive))){
									if(Number::where("gateway", $gatewayObj->id, '!=')->byNumber($data['number'])){
										throw new DuplicateRecord("numbers[{$key}][number]");
									}
								}else{
									throw new InputValidation("numbers[{$key}][status]");
								}
							}else{
								throw new InputValidation("numbers[{$key}][number]");
							}
						}
					}else{
						throw new InputValidation("numbers");
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
							$numberObj = new Number();
							$numberObj->gateway = $gatewayObj->id;
						}
						$numberObj->number = $data['number'];
						$numberObj->status = $data['status'];
						$numberObj->save();
						if(isset($data['primary']) and $data['primary']){
							Options::save('packages.sms.defaultNumber', $number->id);
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
			}catch(InputValidation $error){
				$view->setFormError(FormError::fromException($error));
				$this->response->setStatus(false);
			}catch(DuplicateRecord $error){
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
