<?php

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
