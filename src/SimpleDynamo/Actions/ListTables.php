<?php

/* http://docs.aws.amazon.com/amazondynamodb/latest/APIReference/API_ListTables.html */

namespace SimpleDynamo\Actions;

use \SimpleDynamo\Actions\CommonAction;
use \SimpleDynamo\SimpleDynamo;

class ListTables extends CommonAction
{
	public function __construct($client, $table = null){
		parent::__construct($client, $table);
	}

	public function generateRequest(){
		return $this->request;
	}

	public function extractResponse($response){
		return $response->get('TableNames');
	}
}
