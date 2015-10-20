<?php

namespace SimpleDynamo;

use \SimpleDynamo\SimpleDynamo;
use \Aws\DynamoDb\DynamoDbClient;

class SimpleQuery
{
	private $table;
	private $consistentRead;
	private $startFrom;
	private $limit;
	private $capacity;
	private $names;
	private $values;
	private $columns;
	private $desc;
	private $count;
	private $rawconstraint;
	private $rawfilter;
	private $client;
	private $index;
	private $select;

	public function __construct(SimpleDynamo $client, DynamoDbClient $db, $table){
		$this->client = $client;
		$this->db = $db;
		$this->table = $table;
		$this->consistentRead = false;
		$this->capacity = 'NONE';
		$this->names = array();
		$this->values = array();
		$this->columns = array();
		$this->desc = false;
		$this->count = false;
		$this->rawconstraint = '';
		$this->rawfilter = '';
		$this->index = '';
		$this->select = 'ALL_ATTRIBUTES';
	}

	public function addName($alias, $name){
		$this->names[$alias] = $name;
		return $this;
	}

	public function addNames(array $names){
		foreach($names as $alias => $name){
			$this->addName($alias, $name);
		}
		return $this;
	}

	public function addValue($alias, $value){
		$this->values[$alias] = $this->client->encode($value);
		return $this;
	}

	public function addValues(array $values){
		foreach($values as $alias => $value){
			$this->addValue($alias, $value);
		}
		return $this;
	}

	public function consistent($val = true){
		$this->consistentRead = (bool)$val;
		return $this;
	}

	public function startFrom($indexvalue){
		$this->startFrom = $indexvalue;
		return $this;
	}

	public function limit($val){
		$this->limit = (int)$val;
		return $this;
	}

	public function showCapacity($val){
		if(in_array($val,array('NONE','TOTAL','INDEXES'))){
			$this->capacity = (bool)$val;
		}
		return $this;
	}

	public function addColumn($column){
		$this->columns[$column] = true;
		return $this;
	}

	public function addColumns(array $columns){
		foreach($columns as $column){
			$this->addColumn($column);
		}
		return $this;
	}

	public function index($indexname){
		$this->index = $indexname;
		return $this;
	}

	public function desc($val = true){
		$this->desc = (bool)$val;
		return $this;
	}

	public function count($val = true){
		$this->count = (bool)$val;
		return $this;
	}

	public function rawFilter($filter){
		$this->rawfilter = $filter;
		return $this;
	}

	public function rawConstraint($constraint){
		$this->rawconstraint = $constraint;
		return $this;
	}

	public function getResults(){
		$query = array(
			'TableName'=>(string)$this->table,
			'ReturnConsumedCapacity'=>$this->capacity,
			'ScanIndexForward'=>!$this->desc,
			'ConsistentRead'=>(bool)$this->consistentRead,
		);

		if(!empty($this->limit)){
			$query['Limit'] = (int)$this->limit;
		}

		if(!empty($this->columns)){
			$query['Select'] = 'ALL_PROJECTED_ATTRIBUTES';
			$query['ProjectionExpression'] = implode(', ',$this->columns);
		}

		if(!empty($this->index)){
			$query['IndexName'] = (string)$this->index;
		}

		if(!empty($this->startFrom)){
			$query['ExclusiveStartKey'] = $this->client->encode($this->startFrom);
		}

		if(!empty($this->names)){
			$query['ExpressionAttributeNames'] = $this->names;
		}

		if(!empty($this->values)){
			$query['ExpressionAttributeValues'] = $this->values;
		}

		if(!empty($this->count)){
			unset($query['ProjectionExpression']);
			$query['Select'] = 'COUNT';
		}

		if(!empty($this->rawfilter)){
			$query['FilterExpression'] = $this->rawfilter;
		}

		if(!empty($this->rawconstraint)){
			$query['KeyConditionExpression'] = $this->rawconstraint;
		}

		try{
			$res = $this->db->query($query);
			if($res['@metadata']['statusCode'] !== 200){
				$this->client->errorhandler->__invoke($res);
			}
			$results = array();
			foreach($res->get('Items') as $item){
				foreach($item as $key=>$val){
					$results[$key] = $this->client->decode($val);
				}
			}
			return $results;
		}
		catch(\Exception $e){
			$this->client->errorhandler->__invoke($e->getMessage());
		}
	}
}
