<?php

/*  */

namespace SimpleDynamo\Actions;

use \SimpleDynamo\Actions\CommonAction;
use \SimpleDynamo\SimpleDynamo;

class DescribeTable
{
	use CommonAction;

	private function extractResponse($response){
		return $response;
	}
}
