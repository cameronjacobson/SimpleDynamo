<?php

/* http://docs.aws.amazon.com/amazondynamodb/latest/APIReference/API_GetItem.html */

namespace SimpleDynamo\Actions;

use \SimpleDynamo\Actions\CommonAction;
use \SimpleDynamo\SimpleDynamo;

class GetItem extends CommonAction
{
	public function __construct($client, $table = null){
		parent::__construct($client, $table);
	}

	public function projectionExpression($val){
		if(is_callable($val)){
			$this->expressions($val);
		}
		else{
			$this->expression($val);
		}
		return $this;
	}

	public function generateRequest(){
		$request = array(
			'ConsistentRead' => $this->consistentRead,
			'ExpressionAttributeNames' => $this->expressionAttributeNames,
			'Key' => $this->key,
			'ProjectionExpression' => $this->projectionExpression,
			'ReturnConsumedCapacity' => $this->returnConsumedCapacity,
			'TableName'=>$this->table,
		);
		return $request;
	}

	public function extractResponse($response, $debug = false){
		if(!empty($response->get('ConsumedCapacity'))){
			$this->client->E($response->get('ConsumedCapacity'));
		}
		if($debug){
			return $response;
		}
		$item = array();
		foreach($response->get('Item') as $k=>$v){
			$item[$k] = $this->client->decode($v);
		}
		return $item;
	}
}
