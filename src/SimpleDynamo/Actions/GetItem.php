<?php

/* http://docs.aws.amazon.com/amazondynamodb/latest/APIReference/API_GetItem.html */

namespace SimpleDynamo\Actions;

use \SimpleDynamo\Actions\CommonAction;
use \SimpleDynamo\SimpleDynamo;

class GetItem extends CommonAction
{
	public function __construct($client, $table = null, $async = false){
		parent::__construct($client, $table, $async);
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
			'Key' => $this->get_keys,
			'ReturnConsumedCapacity' => empty($this->returnConsumedCapacity) ? 'NONE' : $this->returnConsumedCapacity,
			'TableName'=>$this->table,
		);

		if(!empty($this->projectionExpression)){
			$request['ProjectionExpression'] = $this->projectionExpression;
		}

		if(!empty($this->expressionAttributeNames)){
			$request['ExpressionAttributeNames'] = $this->expressionAttributeNames;
		}

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
		if(!empty($response->get('Item'))){
			foreach($response->get('Item') as $k=>$v){
				$item[$k] = $this->client->decode($v);
			}
		}
		return $item;
	}
}
