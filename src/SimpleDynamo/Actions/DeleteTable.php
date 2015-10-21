<?php

/* http://docs.aws.amazon.com/amazondynamodb/latest/APIReference/API_DeleteTable.html */

namespace SimpleDynamo\Actions;

use \SimpleDynamo\Actions\CommonAction;
use \SimpleDynamo\SimpleDynamo;

class DeleteTable
{
	use CommonAction;

	private function extractResponse($response){
		return $response;
	}
}
