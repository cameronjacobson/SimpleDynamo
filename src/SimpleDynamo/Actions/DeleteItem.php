<?php

/* http://docs.aws.amazon.com/amazondynamodb/latest/APIReference/API_DeleteItem.html */

namespace SimpleDynamo\Actions;

use \SimpleDynamo\Actions\CommonAction;
use \SimpleDynamo\SimpleDynamo;

class DeleteItem extends CommonAction
{
	public function __construct($client, $table = null, $async = false){
		parent::__construct($client, $table, $async);
	}

	public function conditional($exp){
		if(is_callable($exp)){
			$this->expressions($exp);
		}
		else{
			$this->expression($exp);
		}
		return $this;
	}

	public function generateRequest(){
		$request = array(
			'ConditionExpression'=>$this->expression,
		);
		if(!empty($this->expressionAttributeNames)){
			$request['ExpressionAttributeNames'] = $this->expressionAttributeNames;
		}
		if(!empty($this->expressionAttributeValues)){
			$request['ExpressionAttributeValues'] = $this->expressionAttributeValues;
		}
		if(!empty($this->key)){
			$request['Key'] = $this->key;
		}
		if(!empty($this->returnConsumedCapacity)){
			$request['ReturnConsumedCapacity'] = $this->returnConsumedCapacity;
		}
		if(!empty($this->returnItemCollectionMetrics)){
			$request['ReturnItemCollectionMetrics'] = $this->returnItemCollectionMetrics;
		}
		$request['TableName'] = $this->table;
		return $request;
	}

	public function extractResponse($response,$debug = false){
		return empty($debug) ? true : $response;
	}
}
