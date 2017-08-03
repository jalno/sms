<?php
namespace packages\sms\gateway;
use \packages\sms\gateway;
use \packages\sms\sent;
abstract class handler{
	abstract public function __construct(gateway $gateway);
	abstract public function send(sent $sms);
}
class GatewayException extends \Exception{
	private $data;
	public function __construct($data){
		$this->data = $data;
	}
	public function getData($data){
		return $this->data;
	}
}
