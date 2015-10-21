<?php

/* http://docs.aws.amazon.com/amazondynamodb/latest/APIReference/API_CreateTable.html */

namespace SimpleDynamo\Actions;

use \SimpleDynamo\Actions\CommonAction;
use \SimpleDynamo\SimpleDynamo;

class CreateTable
{
	use CommonAction;

	private function extractResponse($response){
		return $response;
	}
}
