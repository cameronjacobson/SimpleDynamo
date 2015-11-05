<?php

namespace SimpleDynamo;

use \Aws\DynamoDb\DynamoDbClient;
use \Aws\DynamoDb\Marshaler;
use \SimpleDynamo\SimpleQuery;

/**
 *  https://docs.aws.amazon.com/aws-sdk-php/v3/api/api-dynamodb-2012-08-10.html
 */

class SimpleDynamo
{
	private $dbhandle;
	private $table;
	private $marshaler;
	private $consistentread;

	public function __construct(array $params){
		$this->errorhandler = $params['error'];
		$this->errorhandler->bindTo($this);
		try{
			$this->dbhandle = DynamoDbClient::factory(array(
				'profile'=> empty($params['profile']) ? 'default' : $params['profile'],
				'region'=>$params['region'],
				'version'=>'2012-08-10'
			));
		}
		catch(\Exception $e){
			$this->E($e->getMessage());
		}
		$this->table = $params['table'];
		$this->key = $params['key'];
		$this->marshaler = new Marshaler();
		$this->consistentread = isset($params['consistentread']) ? (bool)$params['consistentread'] : true;
	}

	public function getDbHandle(){
		return $this->dbhandle;
	}

	public function getConsistentRead(){
		return $this->consistentread;
	}

	public function setConsistentRead($v){
		$this->consistentread = (bool)$v;
	}

	public function encode($value){
		if(is_null($value) || (empty($value) && is_string($value))){
			return array('NULL'=>true);
		}
		return $this->marshaler->marshalValue($value);
	}

	public function decode($value){
		return empty($value) ? array() : $this->marshaler->unmarshalValue($value);
	}

	public function get($key,$table = null){
		try{
			$result = $this->dbhandle->getItem(array(
				'ConsistentRead' => $this->consistentread,
				'TableName' => isset($table) ? $table : $this->table,
				'Key'=>array(
					$this->key => $this->encode($key)
				)
			));
			if($result['@metadata']['statusCode'] !== 200){
				$this->errorhandler->__invoke($result);
			}
			else{
				$payload = array();
				foreach((array)$result['Item'] as $k=>$v){
					$payload[$k] = $this->decode($v);
				}
				return isset($payload['__payload__']) ? $payload['__payload__'] : $payload;
			}
		}
		catch(\Exception $e){
			$this->errorhandler->__invoke($e);
		}
	}

	public function set($key,$value,$table = null){
		try{
			$payload = array();
			if(is_array($value) || ($value instanceof Traversable)){
				foreach($value as $k=>$v){
					$payload[$k] = $this->encode($v);
				}
			}
			else{
				$payload = array(
					'__payload__' => $this->encode($value)
				);
			}
			$payload[$this->key] = $this->encode($key);
			$result = $this->dbhandle->putItem(array(
				'TableName' => isset($table) ? $table : $this->table,
				'Item' => $payload
			));
			if($result['@metadata']['statusCode'] !== 200){
				$this->errorhandler->__invoke($result);
			}
		}
		catch(\Exception $e){
			$this->errorhandler->__invoke($e);
		}
	}

	public function delete($key){

		if(is_array($key)){
			$keys = array();
			foreach($key as $k=>$v){
				$keys[$k] = $this->encode($v);
			}
		}
		else{
			$keys = array(
				$this->key => $this->encode($key)
			);
		}
		try{
			$result = $this->dbhandle->deleteItem(array(
				'TableName'=>$this->table,
				'Key'=>$keys
			));
			if($result['@metadata']['statusCode'] !== 200){
				$this->errorhandler->__invoke($result);
			}
		}
		catch(\Exception $e){
			$this->errorhandler->__invoke($e);
		}
	}

	public function simplequery($table){
		$query = new SimpleQuery($this,$this->dbhandle,$table);
		return $query;
	}

	public function __call($name,$args){
		if(preg_match("|[^a-zA-Z]|",$name)){
			$this->errorhandler->__invoke('Method '.$name.' does not exist');
		}
		if(substr($name,-5) === 'Async'){
			$args[] = true;
			$name = substr($name,0,-5);
		}
		else{
			$args[] = false;
		}
		$classname = '\SimpleDynamo\Actions\\'.$name;
		if(!class_exists($classname)){
			$this->errorhandler->__invoke('Method '.$name.' does not exist');
		}
		$class = new \ReflectionClass($classname);
		array_unshift($args, $this);
		return $class->newInstanceArgs($args);
	}

	public function E($value){
		error_log(var_export($value,true));
	}
}
