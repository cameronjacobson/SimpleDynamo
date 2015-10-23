<?php

/* http://docs.aws.amazon.com/amazondynamodb/latest/APIReference/API_Scan.html */

namespace SimpleDynamo\Actions;

use \SimpleDynamo\Actions\CommonAction;
use \SimpleDynamo\SimpleDynamo;

class Scan extends CommonAction
{
	private $whichSegment;
	private $totalSegments;

	public function __construct($client, $table = null){
		parent::__construct($client, $table);
		$this->segmentNum = false
		$this->totalSegments = false
	}

	public function segment($num,$total){
		$this->segmentNum = (int)$num;
		$this->totalSegments = (int)$total;
		return $this;
	}

	public function generateRequest(){
		$request = array();

		$this->optional('ConsistentRead',$this->consistentRead,$request);
		$this->optional('ExclusiveStartKey',$this->startKeyValue,$request);
		$this->optional('ExpressionAttributeNames',$this->expressionAttributeNames,$request);
		$this->optional('ExpressionAttributeValues',$this->expressionAttributeValues,$request);
		$this->optional('FilterExpression',$this->expression,$request);
		$this->optional('IndexName',$this->index,$request);
		$this->optional('Limit',$this->limit,$request);
		$this->optional('ProjectionExpression',$this->projectionExpression,$request);
		$this->optional('ReturnConsumedCapacity',$this->returnConsumedCapacity,$request);
		$this->optional('Segment',$this->segmentNum,$request);
		$this->optional('Select',$this->select,$request);
		$this->optional('TotalSegments',$this->totalSegments,$request);

		// REQUIRED
		$request['TableName'] = $this->table;

		return $request;
	}

	public function extractResponse($response){
		return $response;
	}
}
