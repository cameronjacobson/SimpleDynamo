<?php

/* http://docs.aws.amazon.com/amazondynamodb/latest/APIReference/API_BatchWriteItem.html */

namespace SimpleDynamo\Actions;

use \SimpleDynamo\Actions\CommonAction;
use \SimpleDynamo\SimpleDynamo;

class BatchWriteItem extends CommonAction
{
	private $context;

	public function __construct($client, $table = null,$async = false){
		parent::__construct($client, $table, $async);
		$this->returnConsumedCapacity = 'NONE';
		$this->returnItemCollectionsMetrics = 'NONE';
		$this->requests = array();
	}

	public function table($tablename){
		$this->requests[$tablename] = array();
		$this->context = $tablename;
		return $this;
	}

	public function delete($indexname, $value){
		if(is_array($indexname)){
			$key = array();
			foreach($indexname as $k=>$v){
				$key[$v] = $this->client->encode($value[$k]);
			}
		}
		else{
			$key = array($indexname, $this->client->encode($value));
		}
		$this->requests[$this->context][] = array(
			'DeleteRequest' => array(
				'Key' => $key
			)
		);
	}

	public function put($indexname, $value){
		if(is_array($indexname)){
			$item = array();
			foreach($indexname as $k=>$v){
				$item[$v] = $this->client->encode($value[$k]);
			}
		}
		else{
			$item = array($indexname, $this->client->encode($value));
		}
		$this->requests[$this->context][] = array(
			'PutRequest' => array(
				'Item' => $item
			)
		);
	}

	public function multiDelete($indexname, array $values){
		foreach($values as $value){
			$this->delete($indexname, $value);
		}
	}

	public function multiPut($indexname, array $values){
		foreach($values as $value){
			$this->put($indexname, $value);
		}
	}

	public function consumed($val){
		if(in_array($val,$this->validConsumedCapacity)){
			$this->requests['ReturnConsumedCapacity'] = $val;
		}
	}

	public function metrics($val){
		if(in_array($val,$this->validItemCollectionMetrics)){
			$this->requests['ReturnItemCollectionMetrics'] = $val;
		}
	}

	private function generateRequest(){
		foreach($this->requests as &$request){
			if(empty($request['ExpressionAttributeNames'])){
				unset($request['ExpressionAttributeNames']);
			}
		}
		$request = array(
			'RequestItems' => $this->requests,
			'ReturnConsumedCapacity' => $this->returnConsumedCapacity,
			'ReturnItemCollectionMetrics' => $this->returnItemCollectionMetrics
		);
		return $request;
	}

	private function extractResponse($response){
		return $response;
	}
}
