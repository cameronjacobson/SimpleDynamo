<?php

namespace SimpleDynamo\Actions;

use \SimpleDynamo\Actions\CommonAction;
use \SimpleDynamo\SimpleDynamo;

class BatchGetItem extends CommonAction
{
	private $context;

	public function __construct($client, $table = null){
		parent::__construct($client, $table);
		$this->returnConsumedCapacity = 'NONE';
		$this->requests = array();
	}

	public function table($tablename){
		$this->requests[$tablename] = array(
			'ConsistentRead' => false,
			'ExpressionAttributeNames' => array(),
			'Keys'=>array(),
		);
		$this->context = $tablename;
		return $this;
	}

	public function consistent($val = true){
		$this->requests[$this->context]['ConsistentRead'] = (bool)$val;
		return $this;
	}

	public function addName($alias, $name){
		$this->requests[$this->context]['ExpressionAttributeNames'][$alias] = $name;
		return $this;
	}

	public function addKeys($indexname, array $keys){
		foreach($keys as $key){
			$this->addKey($indexname, $key);
		}
		return $this;
	}

	public function addKey($indexname,$value){
		if(is_array($indexname)){
			$vals = array();
			foreach($index as $k=>$idx){
				$vals[$idx] = $this->client->encode($value[$k]);
			}
		}
		else{
			$vals = array($indexname,$this->client->encode($value));
		}
		$this->requests[$this->context]['Keys'][] = $vals;
		return $this;
	}

	public function projection(array $projections){
		$this->requests[$this->context]['ProjectionExpression'] = implode(',',$projections);
		return $this;
	}

	private function generateRequest(){
		foreach($this->requests as &$request){
			if(empty($request['ExpressionAttributeNames'])){
				unset($request['ExpressionAttributeNames']);
			}
		}
		$request = array(
			'RequestItems' => $this->requests,
			'ReturnConsumedCapacity' => $this->returnConsumedCapacity
		);
		return $request;
	}

	private function extractResponse($response){
		return $response;
	}
}
