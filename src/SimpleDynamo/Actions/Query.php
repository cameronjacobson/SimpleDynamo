<?php

/* http://docs.aws.amazon.com/amazondynamodb/latest/APIReference/API_Query.html */

namespace SimpleDynamo\Actions;

use \SimpleDynamo\Actions\CommonAction;
use \SimpleDynamo\SimpleDynamo;

class Query extends CommonAction
{
	public function __construct($client, $table = null, $async = false){
		parent::__construct($client, $table, $async);
	}

	public function generateRequest(){
		$request = array();

		$this->optional('ConsistentRead',$this->consistentRead,$request);
		$this->optional('ExclusiveStartKey',$this->startKeyValue,$request);
		$this->optional('ExpressionAttributeNames',$this->expressionAttributeNames,$request);
		$this->optional('ExpressionAttributeValues',$this->expressionAttributeValues,$request);
		$this->optional('FilterExpression',$this->expression,$request);
		$this->optional('KeyConditionExpression',$this->conditionExpression,$request);
		$this->optional('IndexName',$this->index,$request);
		$this->optional('Limit',empty($this->limit) ? null : $this->limit,$request);
		$this->optional('ProjectionExpression',empty($this->projectionExpression) ? null : $this->projectionExpression,$request);
		$this->optional('ReturnConsumedCapacity',$this->returnConsumedCapacity,$request);
		$this->optional('Select',$this->select,$request);

		// BOOL
		$request['ScanIndexForward'] = $this->asc;

		// REQUIRED
		$request['TableName'] = $this->table;

		return $request;
	}

	public function extractResponse($response,$debug = false){
		if($debug){
			return $response;
		}
		$items = $response->get('Items');
		$return = array();
		foreach($items as $k1=>$item){
			foreach($item as $k2=>$v){
				$return[$k1][$k2] = $this->client->decode($v);
			}
		}
		return $return;
	}
}
