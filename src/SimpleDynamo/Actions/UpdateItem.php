<?php

/* http://docs.aws.amazon.com/amazondynamodb/latest/APIReference/API_UpdateItem.html */

namespace SimpleDynamo\Actions;

use \SimpleDynamo\Actions\CommonAction;
use \SimpleDynamo\SimpleDynamo;

class UpdateItem
{
	use CommonAction;

	private function extractResponse($response){
		return $response;
	}
}
