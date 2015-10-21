<?php

/* http://docs.aws.amazon.com/amazondynamodb/latest/APIReference/API_UpdateItem.html */

namespace SimpleDynamo\Actions;

use \SimpleDynamo\Actions\CommonAction;
use \SimpleDynamo\SimpleDynamo;

class UpdateItem extends CommonAction
{
	private $cExpression;
	private $uExpression;

	public function __construct($client, $table = null){
		parent::__construct($client, $table);
	}

	public function conditionExpression($exp){
		if(is_callable($exp)){
			$this->expressions($exp);
		}
		else{
			$this->expression($exp);
		}
		$this->cExpression = $this->expression;
	}

	public function updateExpression($exp){
		if(is_callable($exp)){
			$this->expressions($exp);
		}
		else{
			$this->expression($exp);
		}
		$this->uExpression = $this->expression;
	}

	public function generateRequest(){
		$request = array();
		if(!empty($this->cExpression)){
			$request['ConditionExpression'] = $this->cExpression;
		}
		if(!empty($this->uExpression)){
			$request['UpdateExpression'] = $this->uExpression;
		}
		if(!empty($this->names)){
			$request['ExpressionAttributeNames'] = $this->names;
		}
		if(!empty($this->values)){
			$request['ExpressionAttributeValues'] = $this->values;
		}
		if(!empty($this->key)){
			$request['Key'] = $this->key;
		}
		$request['ReturnConsumedCapacity'] = $this->returnConsumedCapacity;
		$request['ReturnItemCollectionMetrics'] = $this->returnItemCollectionMetrics;
		$request['ReturnValues'] = $this->returnValues;
		$request['TableName'] = $this->table;
		return array('UpdateItem' => $request);
	}

	private function extractResponse($response){
		return $response;
	}
}
