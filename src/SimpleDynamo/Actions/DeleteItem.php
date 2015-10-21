<?php

namespace SimpleDynamo\Actions;

use \SimpleDynamo\Actions\CommonAction;
use \SimpleDynamo\SimpleDynamo;

class DeleteItem
{
	use CommonAction;

	private function extractResponse($response){
		return $response;
	}
}
