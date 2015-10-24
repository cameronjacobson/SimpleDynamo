<?php

namespace SimpleDynamo\Actions;

use \SimpleDynamo\SimpleDynamo;
use \Aws\DynamoDb\Exception\DynamoDbException;

class CommonAction
{
	public $client;
	private $db;
	protected $table;
	protected $expression;
	private $request;
	protected $consistentRead;
	protected $item;
	protected $expressionAttributeNames;
	protected $expressionAttributeValues;
	protected $returnConsumedCapacity;
	protected $index;

	public function __construct(SimpleDynamo $client, $table = null){
		$this->client = $client;
		$this->db = $client->getDbHandle();
		$this->debug = false;
		$this->table = $table;
		$this->remoteMethod = $this->getRemoteMethodName(get_called_class());
		$this->request = array();
		$this->consistentRead = false;
		$this->keys = array();
		$this->expressionAttributeNames = array();
		$this->expressionAttributeValues = array();
		$this->returnConsumedCapacity = false;
		$this->returnValues = 'NONE';
        $this->asc = true;
        $this->count = false;
        $this->allattributes = false;
        $this->startKeyValue = false;
        $this->projectedattributes = false;
		$this->validConsumedCapacity = array(
			'NONE','TOTAL','INDEXES'
		);
		$this->validItemCollectionMetrics = array(
			'SIZE','NONE'
		);
		$this->validReturnValues = array(
			'NONE','ALL_OLD'
		);
        $this->validSelectionCriteria = array(
            'ALL_ATTRIBUTES','ALL_PROJECTED_ATTRIBUTES','SPECIFIC_ATTRIBUTES','COUNT'
        );
	}

	public function debug(){
		$this->debug = true;
		return $this;
	}

	public function limit($val){
		$this->limit = (int)$val;
		return $this;
	}

	public function setReturnValues($val){
		if(in_array($val,$this->validReturnValues)){
			$this->returnValues = $val;
		}
		return $this;
	}

	public function consistent($val = true){
		$this->consistentRead = (bool)$val;
		return $this;
	}

	public function consumed($val){
		if(in_array($val,$this->validConsumedCapacity)){
			$this->returnConsumedCapacity = $val;
		}
		return $this;
	}

	public function metrics($val){
		if(in_array($val,$this->validItemCollectionMetrics)){
			$this->returnItemCollectionMetrics = $val;
		}
		return $this;
	}

	public function expression($expression){
		$this->expression = $expression;
	}

	public function expressions(callable $fn){
		$this->expression = call_user_func($fn->bindTo($this));
		return $this;
	}

	public function filters(callable $fn){
		$this->filterExpression = call_user_func($fn->bindTo($this));
		return $this;
	}

	public function conditions(callable $fn){
		$this->conditionExpression = call_user_func($fn->bindTo($this));
		return $this;
	}

	public function _and_(array $expressions){
		return '(( '.implode(') AND (',$expressions).' ))';
	}

	public function __and(array $expressions){
		return implode(' AND ',$expressions);
	}

	public function _or_(array $expressions){
		return '( '.implode(' OR ',$expressions).' )';
	}

	public function __or(array $expressions){
		return implode(' OR ',$expressions);
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
			return $this->extractResponse($response,$this->debug);
		}
		catch(DynamoDbException $e){
			switch($e->getAwsErrorCode()){
				case 'ConditionalCheckFailedException':
					$this->client->E('Conditional Check Failed: '.$e->getMessage());
					return false;
					break;
				case 'ResourceNotFoundException':
					$this->client->E('Resource Not Found: '.$e->getMessage());
					return false;
					break;
				case 'ValidationException':
					var_dump($e->getMessage());
					break;
				default:
					var_dump($e->getStatusCode());
					var_dump($e->getAwsRequestId());
					var_dump($e->getAwsErrorType());
					var_dump($e->getAwsErrorCode());
					break;
			}
		}
		catch(\Exception $e){
			$this->client->errorhandler->__invoke($e);
		}
	}

	protected function optional($key,$value,&$var){
		if(!empty($value)){
			$var[$key] = $value;
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
			$this->addKey($k,$v,true);
		}
	}

	public function addItem(array $val){
		$this->item = array();
		foreach($val as $k=>$v){
			$this->item[$k] = $this->client->encode($v);
		}
		return $this;
	}

	public function addKey($key,$value,$multi = false){
		if($multi){
			$this->keys[] = array($key,$this->client->encode($value));
		}
		else{
			$this->key = array(
				$key => $this->client->encode($value)
			);
		}
		return $this;
	}

	public function projection($expressions){
		if(is_callable($expressions)){
			$this->projectionExpression = call_user_func($expressions->bindTo($this));
		}
		else if(is_string($expressions)){
			$this->projectionExpression = $expressions;
		}
		else{
			$this->projectionExpression = implode(',',$expressions);
		}
		return $this;
	}

	public function all(){
		$this->select = 'ALL_ATTRIBUTES';
		return $this;
	}

	public function projected(){
		$this->select = 'ALL_PROJECTED_ATTRIBUTES';
		return $this;
	}

	public function specific(){
		$this->select = 'SPECIFIC_ATTRIBUTES';
		return $this;
	}

	public function startKey($keyname,$val = null){
		if(is_array($keyname)){
			$startkey = array();
			foreach($keyname as $k=>$v){
				$startkey[$k] = $this->client->encode($v);
			}
		}
		else{
			$startkey = array($keyname => $this->client->encode($val));
		}
		$this->startKeyValue = $startkey;
		return $this;
	}

	public function count(){
		$this->select = 'COUNT';
		return $this;
	}

	public function desc($asc = false){
		$this->asc = (bool)$asc;
		return $this;
	}

	public function select($val){
		if(in_array($val,$this->validSelectionCriteria)){
			$this->select = $val;
		}
		return $this;
	}
	public function setIndex($val){
		$this->index = $val;
		return $this;
	}


}
