<?php

/* http://docs.aws.amazon.com/amazondynamodb/latest/APIReference/API_UpdateItem.html */

namespace SimpleDynamo\Actions;

use \SimpleDynamo\Actions\CommonAction;
use \SimpleDynamo\SimpleDynamo;

class UpdateItem extends CommonAction
{
	private $cExpression;
	private $uExpression;

	public function __construct($client, $table = null, $async = false){
		parent::__construct($client, $table, $async);
		$this->setUpdates = null;
		$this->addUpdates = null;
		$this->deleteUpdates = null;
		$this->removeUpdates = null;
		$this->uExpression = '';
	}

	public function conditions($exp){
		if(is_callable($exp)){
			$this->expressions($exp);
		}
		else{
			$this->expression($exp);
		}
		$this->cExpression = $this->expression;
	}


	/**
	 * Ex: SET list[0] = :val1
	 */
	public function _set_(array $expressions){
		$this->setUpdates = ' SET '.implode(', ',$expressions);
	}

	/**
	 * Ex: REMOVE #m.nestedField1, #m.nestedField2
	 */
	public function _remove_(array $attributes){
		$this->removeUpdates = ' REMOVE '.implode(', ',$attributes);
	}

	/**
	 * Ex: ADD aNumber :val1, anotherNumber :val3
	 */
	public function _add_(array $expressions){
		$this->addUpdates = ' ADD '.implode(', ',$expressions);
	}

	/**
	 * Ex: DELETE aSet :val1, :val2
	 */
	public function _delete_(array $values){
		$this->deleteUpdates = ' DELETE '.implode(', ',$values);
	}


	public function updates($exp){
		if(is_callable($exp)){
			$this->expressions($exp);
		}
		else{
			$this->expression($exp);
		}
		$this->uExpression = $this->expression;
		return $this;
	}

	public function generateRequest(){
		$request = array();
		if(!empty($this->cExpression)){
			$request['ConditionExpression'] = $this->cExpression;
		}

		if(!empty($this->addUpdates)){
			$this->uExpression .= $this->addUpdates;
		}
		if(!empty($this->setUpdates)){
			$this->uExpression .= $this->setUpdates;
		}
		if(!empty($this->removeUpdates)){
			$this->uExpression .= $this->removeUpdates;
		}
		if(!empty($this->deleteUpdates)){
			$this->uExpression .= $this->deleteUpdates;
		}
		if(!empty($this->uExpression)){
			$request['UpdateExpression'] = $this->uExpression;
		}

		if(!empty($this->expressionAttributeNames)){
			$request['ExpressionAttributeNames'] = $this->expressionAttributeNames;
		}
		if(!empty($this->expressionAttributeValues)){
			$request['ExpressionAttributeValues'] = $this->expressionAttributeValues;
		}
		if(!empty($this->update_key)){
			$request['Key'] = $this->update_key;
		}
		$request['ReturnConsumedCapacity'] = empty($this->returnConsumedCapacity) ? 'NONE' : $this->returnConsumedCapacity;
		$request['ReturnItemCollectionMetrics'] = empty($this->returnItemCollectionMetrics) ? 'NONE' : $this->returnItemCollectionMetrics;
		$request['ReturnValues'] = $this->returnValues;
		$request['TableName'] = $this->table;
		return $request;
	}

	public function extractResponse($response){
		return $response;
	}
}
