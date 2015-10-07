<?php

namespace SimpleDynamo;

use \Aws\DynamoDb\DynamoDbClient;
use \Aws\DynamoDb\Marshaler;

class SimpleDynamo
{
	private $client;
	private $table;
	private $marshaler;

	public function __construct(array $params){
		$this->errorhandler = $params['error'];
		try{
			$this->client = DynamoDbClient::factory(array(
				'profile'=> empty($params['profile']) ? 'default' : $params['profile'],
				'region'=>$params['region'],
				'version'=>'2012-08-10'
			));
		}
		catch(\Exception $e){
			$this->E($e->getMessage());
		}
		$this->table = $params['table'];
		$this->key = $params['key'];
		$this->marshaler = new Marshaler();
	}

	private function encode($value){
		return $this->marshaler->marshalValue($value);
	}

	private function decode($value){
		return $this->marshaler->unmarshalValue($value);
	}

	public function get($key,$consistentread = true){
		try{
			$result = $this->client->getItem(array(
				'ConsistentRead' => $consistentread,
				'TableName' => $this->table,
				'Key'=>array(
					$this->key => $this->encode($key)
				)
			));
			if($result['@metadata']['statusCode'] !== 200){
				$this->errorhandler->__invoke($result);
			}
			else{
				return $this->decode($result['Item']['payload']);
			}
		}
		catch(\Exception $e){
			$this->errorhandler->__invoke($e);
		}
	}

	public function set($key,$value){
		try{
			$result = $this->client->putItem(array(
				'TableName'=>$this->table,
				'Item'=>array(
					$this->key => $this->encode($key),
					'payload'=>$this->encode($value)
				)
			));
			if($result['@metadata']['statusCode'] !== 200){
				$this->errorhandler->__invoke($result);
			}
		}
		catch(\Exception $e){
			$this->errorhandler->__invoke($e);
		}
	}

	public function delete($key){
		try{
			$result = $this->client->deleteItem(array(
				'TableName'=>$this->table,
				'Key'=>array(
					$this->key => $this->encode($key)
				)
			));
			if($result['@metadata']['statusCode'] !== 200){
				$this->errorhandler->__invoke($result);
			}
		}
		catch(\Exception $e){
			$this->errorhandler->__invoke($e);
		}
	}

	public function createTable($params){
		try{
			$result = $this->client->createTable(array(
				'TableName' => $this->table,
				'AttributeDefinitions' => array_map(function($v){
					return array(
						'AttributeName' => $v[0],
						'AttributeType' => 'S',
					);
				},$params['keys']),
				'KeySchema' => array_map(function($v){
					return array(
						'AttributeName' => $v[0],
						'KeyType'       => $v[1],
					);
				},$params['keys']),
				'ProvisionedThroughput' => array(
					'ReadCapacityUnits'  => $params['readcapacity'],
					'WriteCapacityUnits' => $params['writecapacity']
				)
			));
			if($result['@metadata']['statusCode'] !== 200){
				$this->errorhandler->__invoke($result);
			}
		}catch(\Exception $e){
			$this->errorhandler->__invoke($e);
		}
	}

	private function E($value){
		error_log(var_export($value,true));
	}
}
