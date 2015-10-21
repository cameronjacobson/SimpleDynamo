<?php

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
