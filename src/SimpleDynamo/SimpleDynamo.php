<?php

namespace SimpleDynamo;

use \Aws\DynamoDb\DynamoDbClient;
use \Aws\DynamoDb\Marshaler;

class SimpleDynamo
{
	private $client;
	private $table;
	private $marshaler;
	private $consistentread;

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
		$this->consistentread = isset($params['consistentread']) ? (bool)$params['consistentread'] : true;
	}

	public function getConsistentRead(){
		return $this->consistentread;
	}

	public function setConsistentRead($v){
		$this->consistentread = (bool)$v;
	}

	private function encode($value){
		return $this->marshaler->marshalValue($value);
	}

	private function decode($value){
		return empty($value) ? array() : $this->marshaler->unmarshalValue($value);
	}

	public function get($key,$table = null){
		try{
			$result = $this->client->getItem(array(
				'ConsistentRead' => $this->consistentread,
				'TableName' => isset($table) ? $table : $this->table,
				'Key'=>array(
					$this->key => $this->encode($key)
				)
			));
			if($result['@metadata']['statusCode'] !== 200){
				$this->errorhandler->__invoke($result);
			}
			else{
				$payload = array();
				foreach((array)$result['Item'] as $k=>$v){
					$payload[$k] = $this->decode($v);
				}
				return isset($payload['__payload__']) ? $payload['__payload__'] : $payload;
			}
		}
		catch(\Exception $e){
			$this->errorhandler->__invoke($e);
		}
	}

	public function set($key,$value,$table = null){
		try{
			$payload = array();
			if(is_array($value) || ($value instanceof Traversable)){
				foreach($value as $k=>$v){
					$payload[$k] = $this->encode($v);
				}
			}
			else{
				$payload = array(
					'__payload__' => $this->encode($value)
				);
			}
			$payload[$this->key] = $this->encode($key);
			$result = $this->client->putItem(array(
				'TableName' => isset($table) ? $table : $this->table,
				'Item' => $payload
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
