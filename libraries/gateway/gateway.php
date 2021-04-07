<?php
namespace packages\sms;

use packages\base\{db\DBObject, Exception};
use packages\sms\{gateway\Param};

class Gateway extends DBObject {
	const active = 1;
	const deactive = 2;

	protected $dbTable = "sms_gateways";
	protected $primaryKey = "id";
	private $handlerClass;
	protected $dbFields = array(
		'title' => array('type' => 'text', 'required' => true),
		'handler' => array('type' => 'text', 'required' => true),
        'status' => array('type' => 'int', 'required' => true)
    );
	protected $relations = array(
		'numbers' => array('hasMany', 'packages\\sms\\gateway\\number', 'gateway'),
		'params' => array('hasMany', 'packages\\sms\\gateway\\param', 'gateway')
	);

	function __construct($data = null, $connection = 'default'){
		$data = $this->processData($data);
		parent::__construct($data, $connection);
	}
	protected $tmparams = array();
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

		if(!$this->id){
			$this->tmparams[$name] = $param;
		}else{
			$param->gateway = $this->id;
			return $param->save();
		}
	}
	public function save($data = null) {
		if($return = parent::save($data)){
			foreach($this->tmparams as $param){
				$param->gateway = $this->id;
				$param->save();
			}
			$this->tmparams = array();
		}
		return $return;
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
	public function getController(){
		if($this->handlerClass){
			return $this->handlerClass;
		}
		if(class_exists($this->handler)){
			$this->handlerClass = new $this->handler($this);
			return $this->handlerClass;
		}
		return false;
	}
	public function send(sent $sms) {
		$controller = $this->getController();
		if ($controller === false) {
			throw new Exception("can not find handler");
		}
		return $controller->send($sms);
	}
}
