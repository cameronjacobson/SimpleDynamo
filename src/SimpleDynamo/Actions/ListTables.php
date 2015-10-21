<?php

namespace SimpleDynamo\Actions;

use \SimpleDynamo\Actions\CommonAction;
use \SimpleDynamo\SimpleDynamo;

class ListTables
{
	use CommonAction;

	private function extractResponse($response){
		return $response->get('TableNames');
	}
}
