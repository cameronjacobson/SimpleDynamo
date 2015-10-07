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
		$this->client = DynamoDbClient::factory(array(
			'profile'=> empty($params['profile']) ? 'default' : $params['profile'],
			'region'=>$params['region'],
			'version'=>'2012-08-10'
		));
		$this->table = $params['table'];
		$this->key = $params['key'];
		$this->marshaler = new Marshaler();
		$this->errorhandler = $params['error'];
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
				$this->errorhandler($result);
			}
			else{
				return $this->decode($result['Item']['payload']);
			}
		}
		catch(\Exception $e){
			$this->errorhandler($e);
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
				$this->errorhandler($result);
			}
		}
		catch(\Exception $e){
			$this->errorhandler($e);
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
				$this->errorhandler($result);
			}
		}
		catch(\Exception $e){
			$this->errorhandler($e);
		}
	}

	private function E($value){
		error_log(var_export($value,true));
	}
}
