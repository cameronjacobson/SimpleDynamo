<?php

/* http://docs.aws.amazon.com/amazondynamodb/latest/APIReference/API_Query.html */

namespace SimpleDynamo\Actions;

use \SimpleDynamo\Actions\CommonAction;
use \SimpleDynamo\SimpleDynamo;

class Query
{
	use CommonAction;

	private function extractResponse($response){
		return $response;
	}
}
