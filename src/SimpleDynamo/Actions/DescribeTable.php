<?php

/*	*/

namespace SimpleDynamo\Actions;

use \SimpleDynamo\Actions\CommonAction;
use \SimpleDynamo\SimpleDynamo;

class DescribeTable extends CommonAction
{
	public function __construct($client, $table = null){
		parent::__construct($client, $table);
	}

	public function generateRequest(){
		return array(
			'TableName'=>$this->table
		);
	}

	private function extractResponse($response){
		return $response;
	}
}
