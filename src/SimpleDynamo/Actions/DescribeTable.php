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

	public function extractResponse($response, $debug){
		return empty($debug) ? $response->get('Table') : $response;
	}
}
