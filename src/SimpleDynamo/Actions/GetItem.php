<?php

/* http://docs.aws.amazon.com/amazondynamodb/latest/APIReference/API_GetItem.html */

namespace SimpleDynamo\Actions;

use \SimpleDynamo\Actions\CommonAction;
use \SimpleDynamo\SimpleDynamo;

class GetItem
{
	use CommonAction;

	private function extractResponse($response){
		return $response;
	}
}
