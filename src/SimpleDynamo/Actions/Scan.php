<?php

/* http://docs.aws.amazon.com/amazondynamodb/latest/APIReference/API_Scan.html */

namespace SimpleDynamo\Actions;

use \SimpleDynamo\Actions\CommonAction;
use \SimpleDynamo\SimpleDynamo;

class Scan
{
	use CommonAction;

	private function extractResponse($response){
		return $response;
	}
}
