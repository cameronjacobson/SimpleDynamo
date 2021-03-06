<?php

/* http://docs.aws.amazon.com/amazondynamodb/latest/APIReference/API_PutItem.html */

namespace SimpleDynamo\Actions;

use \SimpleDynamo\Actions\CommonAction;
use \SimpleDynamo\SimpleDynamo;

class PutItem extends CommonAction
{
	public function __construct($client, $table = null, $async = false){
		parent::__construct($client, $table, $async);
	}

	public function conditions($val){
		if(is_callable($val)){
			$this->conditionExpression = call_user_func($val->bindTo($this));
		}
		elseif(is_string($val)){
			$this->conditionExpression = $val;
		}
		return $this;
	}

	public function generateRequest(){
		$request = array();
		if(!empty($this->conditionExpression)){
			$request['ConditionExpression'] = $this->conditionExpression;
		}
		if(!empty($this->expressionAttributeNames)){
			$request['ExpressionAttributeNames'] = $this->expressionAttributeNames;
		}
		if(!empty($this->expressionAttributeValues)){
			$request['ExpressionAttributeValues'] = $this->expressionAttributeValues;
		}
		if(!empty($this->item)){
			$request['Item'] = $this->item;
		}
		$request['ReturnConsumedCapacity'] = $this->returnConsumedCapacity ?: 'NONE';
		$request['ReturnItemCollectionMetrics'] = $this->returnItemCollectionMetrics;
		$request['ReturnValues'] = $this->returnValues;
		$request['TableName'] = $this->table;
		return $request;
	}

	public function extractResponse($response){
        if(!empty($response->get('ConsumedCapacity'))){
            $this->client->E($response->get('ConsumedCapacity'));
        }
        if($debug){
            return $response;
        }
		$attrs = $response->get('Attributes');
		
		return empty($attrs) ? true : $this->decodeResponse($attrs);
	}

	public function decodeResponse($response){
		$item = array();
		foreach($response as $k=>$v){
			$item[$k] = $this->client->decode($v);
		}
		return $item;
	}
}
