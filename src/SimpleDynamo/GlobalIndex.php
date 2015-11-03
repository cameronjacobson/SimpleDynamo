<?php

namespace SimpleDynamo;

class GlobalIndex
{
	private $readCapacity;
	private $writeCapacity;
	private $indexName;
	private $projectionType;
	private $schema;
	private $nonKeyAttributes;

	public function __construct($indexName){
		$this->indexName = $indexName;
		$this->readCapacity = 1;
		$this->writeCapacity = 1;
		$this->schema = array();
		$this->projectionType = 'KEYS_ONLY';
		$this->nonKeyAttributes = array();
	}

	public function addKeys(array $keys){
		foreach($keys as $name=>$type){
			$this->addKey($name,$type);
		}
	}

	public function addKey($name,$type){
		$this->schema[] = array(
			'AttributeName'=>$name,
			'KeyType'=>$type
		);
	}

	public function projection($attributes){
		if(is_string($attributes)){
			$this->projectionType = $attributes;
		}
		else if(is_array($attributes)){
			$this->projectionType = 'INCLUDE';
			$this->nonKeyAttributes = $attributes;
		}
	}

	public function throughput($read,$write){
		$this->readCapacity = (int)$read;
		$this->writeCapacity = (int)$write;
	}

	public function getSpec(){
		$spec = array(
			'IndexName'=>$this->indexName
		);
		if(!empty($this->schema)){
			$spec['KeySchema'] = $this->schema;
		}
		$spec['Projection'] = array(
			'ProjectionType' => $this->projectionType
		);
		if(!empty($this->nonKeyAttributes)){
			$spec['Projection']['NonKeyAttributes'] = $this->nonKeyAttributes;
		}
		$spec['ProvisionedThroughput'] = array(
			'ReadCapacityUnits'=>$this->readCapacity,
			'WriteCapacityUnits'=>$this->writeCapacity
		);
		return $spec;
	}

}
