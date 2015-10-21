<?php

/* http://docs.aws.amazon.com/amazondynamodb/latest/APIReference/API_PutItem.html */

namespace SimpleDynamo\Actions;

use \SimpleDynamo\Actions\CommonAction;
use \SimpleDynamo\SimpleDynamo;

class PutItem
{
	use CommonAction;

	private function extractResponse($response){
		return $response;
	}
}
