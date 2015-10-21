<?php

/* http://docs.aws.amazon.com/amazondynamodb/latest/APIReference/API_Scan.html */

namespace SimpleDynamo\Actions;

use \SimpleDynamo\Actions\CommonAction;
use \SimpleDynamo\SimpleDynamo;

class Scan extends CommonAction
{
	public function __construct($client, $table = null){
		parent::__construct($client, $table);
	}

	private function extractResponse($response){
		return $response;
	}
}
