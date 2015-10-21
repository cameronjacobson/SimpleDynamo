<?php

/* http://docs.aws.amazon.com/amazondynamodb/latest/APIReference/API_PutItem.html */

namespace SimpleDynamo\Actions;

use \SimpleDynamo\Actions\CommonAction;
use \SimpleDynamo\SimpleDynamo;

class PutItem extends CommonAction
{
	public function __construct($client, $table = null){
		parent::__construct($client, $table);
	}

	public function conditionExpression($val){
		if(is_callable($val)){
			$this->expressions($val);
		}
		else{
			$this->expression($val);
		}
		return $this;
	}

	public function generateRequest(){
		$request = array();
		if(!empty($this->expression)){
			$request['ConditionExpression'] = $this->expression;
		}
		if(!empty($this->names)){
			$request['ExpressionAttributeNames'] = $this->names;
		}
		if(!empty($this->values)){
			$request['ExpressionAttributeValues'] = $this->values;
		}
		if(!empty($this->item)){
			$request['Item'] = $this->item;
		}
		$request['ReturnConsumedCapacity'] = $this->returnConsumedCapacity;
		$request['ReturnItemCollectionMetrics'] = $this->returnItemCollectionMetrics;
		$request['ReturnValues'] = $this->returnValues;
		$request['TableName'] = $this->table;
		return array('PutItem' => $request);
	}

	private function extractResponse($response){
		return $response;
	}
}
