<?php

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
