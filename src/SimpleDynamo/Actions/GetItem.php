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
			'ExpressionAttributeNames' => $this->names,
			'Key' => $this->key,
			'ProjectionExpression' => $this->expression,
			'ReturnConsumedCapacity' => $this->returnConsumedCapacity,
			'TableName'=>$this->table,
		);
		return $request;
	}

	private function extractResponse($response){
		return $response;
	}
}
