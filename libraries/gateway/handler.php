<?php
namespace packages\sms\gateway;
use \packages\sms\gateway;
use \packages\sms\sent;
abstract class handler{
	abstract public function __construct(gateway $gateway);
	abstract public function send(sent $sms);
}
class GatewayException extends \Exception{}
