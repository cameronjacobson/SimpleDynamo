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
		$this->schema = array();
		$this->streamspec = false;
		$this->validStreamSpecification = array('KEYS_ONLY','NEW_IMAGE','OLD_IMAGE','NEW_AND_OLD_IMAGES');
		$this->validProjectionType = array('KEYS_ONLY','INCLUDE','ALL');
		$this->throughput = array(
			'ReadCapacityUnits'=>1,
			'WriteCapacityUnits'=>1,
		);
	}

	public function addAttributes(array $attributes){
		foreach($attributes as $name=>$type){
			$this->addAttribute($name,$type);
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

	public function addSchema(array $vals){
		foreach($vals as $name=>$type){
			$this->schema[] = array(
				'AttributeName'=>$name,
				'KeyType'=>$type
			);
		}
		return $this;
	}

	public function addGlobal(array $global){
		$result = array();
		foreach($global as $globalname=>$spec){
			$tmp = array(
				'IndexName'=>$globalname,
				'KeySchema'=>array()
			);
			if(!empty($spec['keys'])){
				foreach($spec['keys'] as $name=>$type){
					$tmp['KeySchema'][] = array(
						'AttributeName'=>$name,
						'KeyType'=>$type
					);
				}
			}
			if(!empty($spec['projection'])){
				if(is_array($spec['projection'])){
					$tmp['Projection'] = array(
						'ProjectionType' => 'INCLUDE',
						'NonKeyAttributes' => $spec['projection']
					);
				}
				else if(is_string($spec['projection']) && in_array($spec['projection'],$this->validProjectionType)){
					$tmp['Projection'] = array(
						'ProjectionType' => $spec['projection']
					);
				}
			}
			$tmp['ProvisionedThroughput'] = array(
				'ReadCapacityUnits'=>empty($spec['throughput'][0]) ? 1 : $spec['throughput'][0],
				'WriteCapacityUnits'=>empty($spec['throughput'][1]) ? 1 : $spec['throughput'][1],
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
		return $this;
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
				'KeySchema'=>array()
			);
			if(!empty($spec['keys'])){
				foreach($spec['keys'] as $name=>$type){
					$tmp['KeySchema'][] = array(
						'AttributeName'=>$name,
						'KeyType'=>$type
					);
				}
			}
			if(!empty($spec['projection'])){
				if(is_array($spec['projection'])){
					$tmp['Projection'] = array(
						'ProjectionType' => 'INCLUDE',
						'NonKeyAttributes' => $spec['projection']
					);
				}
				else if(is_string($spec['projection']) && in_array($spec['projection'],$this->validProjectionType)){
					$tmp['Projection'] = array(
						'ProjectionType' => $spec['projection']
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
