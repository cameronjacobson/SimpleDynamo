<?php

/* http://docs.aws.amazon.com/amazondynamodb/latest/APIReference/API_CreateTable.html */

namespace SimpleDynamo\Actions;

use \SimpleDynamo\Actions\CommonAction;
use \SimpleDynamo\SimpleDynamo;

class CreateTable extends CommonAction
{
	private $attributes;
	private $global;
	private $local;
	private $schema;
	private $throughput;
	private $streamspec;

	public function __construct($client, $table = null){
		parent::__construct($client, $table);
		$this->attributes = array();
		$this->globalIndexes = array();
		$this->localIndexes = array();
		$this->throughput = array();
		$this->streamspec = false;
		$this->validStreamSpecification = array('KEYS_ONLY','NEW_IMAGE','OLD_IMAGE','NEW_AND_OLD_IMAGES');
		$this->validProjectionType = array('KEYS_ONLY','INCLUDE','ALL');
		$this->throughput = array(
			'ReadCapacityUnits'=>1,
			'WriteCapacityUnits'=>1,
		);
	}

	public function addAttributes(array $attributes){
		foreach($attributes as $attribute){
			$this->addAttribute($attribute[0],$attribute[1]);
		}
		return $this;
	}

	public function addAttribute($name,$type){
		$this->attributes[] = array(
			'AttributeName'=>$name,
			'AttributeType'=>$type
		);
		return $this;
	}

	public function addSchema(array $schema){

	}

	public function addGlobal(array $global){
		$result = array();
		foreach($global as $globalname=>$spec){
			$tmp = array(
				'IndexName'=>$globalname,
				'KeySchema'=>$this->schema,
			);
			if(!empty($spec['keys'])){
				foreach($spec['keys'] as $name=>$type){
					$tmp['KeySchema'][] = array(
						'AttributeName'=>$name,
						'KeyType'=>$type
					);
				}
			}
			if(!empty($spec['attributes']) && is_array($spec['attributes'])){
				if(is_array($spec['attributes'])){
					$tmp['Projection'] = array(
						'ProjectionType' => 'INCLUDE',
						'NonKeyAttributes' => $spec['attributes']
					);
				}
				else if(is_string($spec['attributes']) && in_array($spec['attributes'],$this->validProjectionType)){
					$tmp['Projection'] = array(
						'ProjectionType' => $spec['attributes'],
						'NonKeyAttributes' => array()
					);
				}
			}
			$tmp['ProvisionedThroughput'] = array(
				'ReadCapacityUnits'=>empty($spec['readcapacity']) ? 1 : $spec['readcapacity'],
				'WriteCapacityUnits'=>empty($spec['writecapacity']) ? 1 : $spec['writecapacity'],
			);

			$result[] = $tmp;
		}
		$this->global = $result;
		return $this;
	}

	public function throughput($read = 1, $write = 1){
		$this->throughput = array(
			'ReadCapacityUnits'=>(int)$read,
			'WriteCapacityUnits'=>(int)$write
		);
	}

	public function addLocal(array $local){
		if(empty($this->schema)){
			// need error here: cant set local indexes without schema
			return;
		}
		$result = array();
		foreach($local as $localname=>$spec){
			$tmp = array(
				'IndexName'=>$localname,
				'KeySchema'=>$this->schema,
			);
			if(!empty($spec['keys'])){
				foreach($spec['keys'] as $name=>$type){
					$tmp['KeySchema'][] = array(
						'AttributeName'=>$name,
						'KeyType'=>$type
					);
				}
			}
			if(!empty($spec['attributes']) && is_array($spec['attributes'])){
				if(is_array($spec['attributes'])){
					$tmp['Projection'] = array(
						'ProjectionType' => 'INCLUDE',
						'NonKeyAttributes' => $spec['attributes']
					);
				}
				else if(is_string($spec['attributes']) && in_array($spec['attributes'],$this->validProjectionType)){
					$tmp['Projection'] = array(
						'ProjectionType' => $spec['attributes'],
						'NonKeyAttributes' => array()
					);
				}
			}

			$result[] = $tmp;
		}
		$this->local = $result;
		return $this;
	}

	public function stream($val,$enabled = true){
		if(in_array($val,$this->validStreamSpecification)){
			$this->streamspec = array(
				'StreamEnabled'=>$enabled,
				'StreamViewType'=>$val
			);
		}
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

	private function extractResponse($response){
		return $response;
	}
}
