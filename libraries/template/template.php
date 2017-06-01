<?php
namespace packages\sms;
use \packages\base\db\dbObject;
class template extends dbObject{
	const active = 1;
	const deactive = 2;
	protected $dbTable = "sms_templates";
	protected $primaryKey = "id";
	protected $dbFields = array(
        'name' => array('type' => 'text', 'required' => true),
		'lang' => array('type' => 'text', 'required' => true),
        'event' => array('type' => 'text'),
        'variables' => array('type' => 'text'),
        'render' => array('type' => 'text'),
		'text' => array('type' => 'text', 'required' => true),
		'status' => array('type' => 'int', 'required' => true)
    );
	private $recursionLevel = 0;
	protected $jsonFields = array('variables');
	public function addObjectVariable($obj,$prefix=''){
		if($primaryKey = $obj->getPrimaryKey()){
			$this->addVariable($prefix.'->'.$primaryKey);
		}
		foreach(array_keys($obj->getFields()) as $field){
			$this->addVariable($prefix.'->'.$field);
		}
		foreach($obj->getRelations() as $field => $relation){
			if(strtolower($relation[0]) == 'hasone'){
				$relation[1] = '\\'.$relation[1];
				$robj = new $relation[1]();
				if(is_a($robj, get_class($obj))){
					if($this->recursionLevel > 0){
						continue;
					}
					$this->recursionLevel++;
				}
				$this->addObjectVariable($robj,$prefix.'->'.$field);
			}
		}
	}
	public function addVariable($name){
		if(strpos($name, "\\") !== false and is_subclass_of($name, '\\packages\\base\\db\\dbObject')){
			$obj = new $name();
			$name = explode("\\", $name);
			$name = $name[count($name)-1];
			$this->addObjectVariable($obj, $name);
			return;
		}
		if(!$this->variables){
			$this->variables = array();
		}
		if(!in_array($name, $this->variables)){
			$variables = $this->variables;
			$variables[] = $name;
			$this->variables = $variables;
		}
	}
	public function render($params = array()){
		if(!$this->variables){
			$this->variables = array();
		}
		if($this->rander){
			list($class,$method) = explode("@", $this->rander, 2);
			if(class_method($class) and method_exists($class,$method)){
				$obj = new $class($this);
				return $obj->$method($params);
			}
		}else{
			$keys = array();
			$values = array();
			foreach($this->variables as $variable){
				$keys[] = '['.$variable.']';
				$value = '';
				if(isset($params[$variable])){
					$value = $params[$variable];
				}else{
					$parts = explode('->',$variable);
					if(
						isset($params[$parts[0]]) and
						is_object($params[$parts[0]]) and
						$params[$parts[0]] instanceof dbObject
					){
						$obj = $params[$parts[0]];
						$len = count($parts);
						for($x = 1;($x < $len and is_object($obj) and $obj instanceof dbObject);$x++){
							$part = $parts[$x];
							$obj = $obj->$part;
						}
						if($x == $len and !is_object($obj)){
							$value = $obj;
						}
					}
				}
				if(is_array($value) or is_object($value)){
					$value = json\encode($value);
				}
				$values[] = $value;
			}
			return str_ireplace($keys,$values,$this->text);

		}
	}
}
