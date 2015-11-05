<?php

/* http://docs.aws.amazon.com/amazondynamodb/latest/APIReference/API_DeleteTable.html */

namespace SimpleDynamo\Actions;

use \SimpleDynamo\Actions\CommonAction;
use \SimpleDynamo\SimpleDynamo;

class DeleteTable extends CommonAction
{
	public function __construct($client, $table = null, $async = false){
		parent::__construct($client, $table, $async);
	}

	public function generateRequest(){
		return array(
			'TableName' => $this->table
		);
	}

	protected function extractResponse($response,$debug = false){
		return empty($debug) ? true : $response;
	}
}
