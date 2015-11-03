<?php

/* http://docs.aws.amazon.com/amazondynamodb/latest/APIReference/API_UpdateTable.html */

namespace SimpleDynamo\Actions;

use \SimpleDynamo\Actions\CommonAction;
use \SimpleDynamo\SimpleDynamo;
use \SimpleDynamo\GlobalIndex;

class UpdateTable extends CommonAction
{
	private $indexUpdates;

	public function __construct($client, $table = null){
		parent::__construct($client, $table);
		$this->indexUpdates = array();
	}

	public function create(callable $fn){
        $index = new GlobalIndex($indexName);
        call_user_func($fn->bindTo($index));
		$this->indexUpdates[] = array(
			'Create'=>$index->getSpec();
		);
        return $this;
	}

	public function delete($indexName){
		$this->indexUpdates[] = array(
			'Delete'=>array(
				'IndexName'=>$indexName
			)
		);
	}

	public function update($indexName,$readCapacity,$writeCapacity){
		$this->indexUpdates[] = array(
			'Update'=>array(
				'IndexName'=>$indexName,
				'ProvisionedThroughput'=>array(
					'ReadCapacityUnits'=>$readCapacity,
					'WriteCapacityUnits'=>$writeCapacity
				)
			)
		);
	}

	public function alterGlobal(callable $fn){
		call_user_func($fn->bindTo($this));
		return $this;
	}

	public function extractResponse($response){
		$response = array(
			'TableName'=>$this->table
		);
		if(!empty($this->indexUpdates)){
			$response['GlobalSecondaryIndexUpdates'] = $this->indexUpdates
		}
		if(!empty($this->throughput)){
			$response['ProvisionedThroughput'] = $this->throughput;
		}
		if(!empty($this->streamspec)){
			$response['StreamSpecification'] = $this->streamspec;
		}
		if(!empty($this->attributes)){
			$response['AttributeDefinitions'] = $this->attributes;
		}
		return $response;
	}
}
