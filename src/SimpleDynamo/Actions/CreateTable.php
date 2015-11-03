<?php

/* http://docs.aws.amazon.com/amazondynamodb/latest/APIReference/API_CreateTable.html */

namespace SimpleDynamo\Actions;

use \SimpleDynamo\Actions\CommonAction;
use \SimpleDynamo\SimpleDynamo;
use \SimpleDynamo\GlobalIndex;
use \SimpleDynamo\LocalIndex;

class CreateTable extends CommonAction
{
	private $global;
	private $local;
	private $schema;

	public function __construct($client, $table = null){
		parent::__construct($client, $table);
		$this->globalIndexes = array();
		$this->localIndexes = array();
		$this->schema = array();
		$this->streamspec = false;
		$this->validStreamSpecification = array('KEYS_ONLY','NEW_IMAGE','OLD_IMAGE','NEW_AND_OLD_IMAGES');
		$this->validProjectionType = array('KEYS_ONLY','INCLUDE','ALL');
		$this->throughput = array(
			'ReadCapacityUnits'=>1,
			'WriteCapacityUnits'=>1,
		);
	}

	public function addSchema(array $vals){
		foreach($vals as $name=>$type){
			$this->schema[] = array(
				'AttributeName'=>$name,
				'KeyType'=>$type
			);
		}
		return $this;
	}

	public function addGlobal($indexName, callable $fn){
		$index = new GlobalIndex($indexName);
		call_user_func($fn->bindTo($index));
		$this->global[] = $index->getSpec();
		return $this;
	}

	public function addLocal($indexName, callable $fn){
		$index = new LocalIndex($indexName);
		call_user_func($fn->bindTo($index));
		$this->local[] = $index->getSpec();
		return $this;
	}

	public function generateRequest(){
		$request = array();
		if(!empty($this->attributes)){
			$request['AttributeDefinitions'] = $this->attributes;
		}
		if(!empty($this->global)){
			$request['GlobalSecondaryIndexes'] = $this->global;
		}
		if(!empty($this->schema)){
			$request['KeySchema'] = $this->schema;
		}
		if(!empty($this->local)){
			$request['LocalSecondaryIndexes'] = $this->local;
		}
		$request['ProvisionedThroughput'] = $this->throughput;
		if(!empty($this->streamspec)){
			$request['StreamSpecification'] = $this->streamspec;
		}
		$request['TableName'] = $this->table;
		return $request;
	}

	public function extractResponse($response,$debug = false){
		$tabledesc = $response->get('TableDescription');
		if($debug){
			return $tabledesc;
		}
		return array(
			'arn'=>$tabledesc['TableArn'],
			'stream_arn'=>$tabledesc['LatestStreamArn']
		);
	}
}
