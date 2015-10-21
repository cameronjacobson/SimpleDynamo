<?php

namespace SimpleDynamo\Actions;

use \SimpleDynamo\SimpleDynamo;

class CommonAction
{
	private $client;
	private $db;
	private $table;
	private $expression;
	private $request;
	private $consistentRead;
	private $expressionAttributeNames;
	private $expressionAttributeValues;
	private $returnConsumedCapacity;

	public function __construct(SimpleDynamo $client, $table = null){
		$this->client = $client;
		$this->db = $client->getDbHandle();
		$this->table = $table;
		$this->remoteMethod = $this->getRemoteMethodName(get_called_class());
		$this->request = array();
		$this->consistentRead = false;
		$this->keys = array();
		$this->expressionAttributeNames = array();
		$this->expressionAttributeValues = array();
		$this->returnConsumedCapacity = false;

		$this->validConsumedCapacity = array(
			'NONE','TOTAL','INDEXES'
		);
		$this->validItemCollectionMetrics = array(
			'SIZE','NONE'
		);
	}

	public function consistent($val = true){
		$this->consistentRead = (bool)$val;
	}

	public function consumed($val){
		if(in_array($val,$this->validConsumedCapacity)){
			$this->returnConsumedCapacity = $val;
		}
	}

	public function metrics($val){
		if(in_array($val,$this->validItemCollectionMetrics)){
			$this->returnItemCollectionMetrics = $val;
		}
	}

	public function expression($expression){
		$this->expression = $expression;
	}

	public function expressions(callable $fn){
		$this->expression = call_user_func(array($this,$fn));
		return $this;
	}

	private function _and_(array $expressions){
		return '('.implode(' AND ',$expressions).')';
	}

	private function _or_(array $expressions){
		return '('.implode(' OR ',$expressions).')';
	}

	private function getRemoteMethodName($class){
		$parts = explode('\\',$class);
		$class = array_pop($parts);
		return lcfirst($class);
	}

	public function getResults(){
		try{
			$response = call_user_func(array($this->db, $this->remoteMethod), $this->generateRequest());
			if($response['@metadata']['statusCode'] !== 200){
				$this->client->errorhandler->__invoke($response);
			}
			return $this->extractResponse($response);
		}
		catch(\Exception $e){
			$this->client->errorhandler->__invoke($e);
		}
	}

	public function addName($alias, $name){
		$this->expressionAttributeNames[$alias] = $name;
		return $this;
	}

	public function addNames(array $names){
		foreach($names as $alias => $name){
			$this->addName($alias, $name);
		}
		return $this;
	}

	public function addValue($alias, $value){
		$this->expressionAttributeValues[$alias] = $this->client->encode($value);
		return $this;
	}

	public function addValues(array $values){
		foreach($values as $alias => $value){
			$this->addValue($alias, $value);
		}
		return $this;
	}

	public function addKeys(array $keys){
		foreach($keys as $k=>$v){
			$this->addKey($k,$v);
		}
	}

	public function addKey($key,$value){
		$this->keys[] = array($key,$this->client->encode($value));
	}

}
