<?php
namespace packages\sms;
use \packages\base\utility\safe;
use \packages\base\options;
use \packages\base\events;
use \packages\userpanel\user;
use \packages\sms\sent;
use \packages\sms\template;
use \packages\sms\gateway;
use \packages\sms\gateway\number;
use \packages\sms\events as smsEvents;

class api{
	private $message;
	private $receiver_number;
	private $receiver_user;
	private $sender_user;
	private $time;
	public function template($name,$parameters = array(),$lang = null){
		if($lang === null){
			$lang = translator::getShortCodeLang();
		}
		if(!$lang){
			throw new unkownLanguage();
		}
		$template = new template();
		$template->where('name', $name);
		$template->where('lang', $lang);
		$template->getOne();
		$this->message = $template->id ? str_replace(array_keys($parameters), array_values($parameters), $template->text) : null;
		return $this;
	}
	public function to($receiver_number, user $receiver_user = null){
		$this->receiver_number = $receiver_number;
		if($receiver_user === null and !is_object($receiver_number)){
			$user = new user();
			$user->where("cellphone", $receiver_number);
			$user->getOne();
			$this->receiver_user = $user;
		}
		return $this;
	}
	public function fromUser(user $sender_user){
		$this->sender_user  = $sender_user;
		return $this;
	}
	public function fromNumber($number){
		if(is_object($number) and $number instanceof number){
			if($number->status == number::active and $number->gateway->status == gateway::active){
				$this->sender_number = $number;
			}else{
				throw new deactivedNumberException;
			}
		}else{
			$this->sender_number = $number;
		}
		return $this;
	}
	public function fromDefaultNumber(){
		if($defaultnumber = options::get('packages.sms.defaultNumber')){
			if($number = number::byID($defaultnumber)){
				$this->fromNumber($number);
			}else{
				throw new defaultNumberException();
			}
		}else{
			throw new defaultNumberException();
		}
	}
	private function checkFromNumber($type){
		if($type == 'receive'){
			if(!safe::is_cellphone_ir($this->sender_number)){
				throw new numberException;
			}
		}elseif($type == 'send'){
			if(!$this->sender_number instanceof number){
				if($sender_number = number::where('number', $this->sender_number)->getOne()){
					if($number->status == number::active and $number->gateway->status == gateway::active){
						$this->sender_number = $sender_number;
					}else{
						throw new deactivedNumberException;
					}
				}else{
					throw new numberException;
				}
			}

		}
	}
	private function checkToNumber($type){
		if($type == 'send'){
			if(!safe::is_cellphone_ir($this->receiver_number)){
				throw new numberException;
			}
		}elseif($type == 'receive'){
			if(!$this->receiver_number instanceof number){
				if($receiver_number = number::where('number', $this->receiver_number)->getOne()){
					if($number->status == number::active and $number->gateway->status == gateway::active){
						$this->receiver_number = $receiver_number;
					}else{
						throw new deactivedNumberException;
					}
				}else{
					throw new numberException;
				}
			}

		}
	}
	public function now(){
		$this->time = time();
		return $this;
	}
	public function at($time){
		$this->time = $time;
		return $this;
	}
	public function receive($message){
		$this->checkFromNumber('receive');
		$this->checkToNumber('receive');
		$sms = new get();
		$sms->receive_at = $this->time;
		$sms->sender_number = $this->sender_number;
		if($this->sender_user){
			$sms->sender_user = $this->sender_user->id;
		}
		$sms->receiver_number = $this->receiver_number->id;
		$sms->text = $message;
		$sms->save();
		events::trigger(new smsEvents\receive($sms));
		return true;
	}
	public function send($message = null){
		$sms = new sent();
		$sms->send_at = $this->time;
		if($this->sender_number){
			$this->checkFromNumber('send');
		}else{
			$this->fromDefaultNumber();
		}

		$sms->sender_number = $this->sender_number->id;
		if($this->sender_user){
			$sms->sender_user = $this->sender_user->id;
		}
		$sms->receiver_number = $this->receiver_number;
		if($this->receiver_user){
			$sms->receiver_user = $this->receiver_user->id;
		}
		$sms->text = $message !== null ? $message : $this->message;
		if($sms->send_at >= time()){
			$sms->status = sent::queued;
		}else{
			$sms->status = sent::sending;
		}
		$sms->save();
		if($sms->send_at >= time()){
			$sms->send();
		}
		events::trigger(new smsEvents\send($sms));
		return $sms->status;
	}
}
class unkownLanguage extends \Exception{

}
class numberException extends \Exception{}
class deactivedNumberException extends \Exception{}
class defaultNumberException extends \Exception{}
