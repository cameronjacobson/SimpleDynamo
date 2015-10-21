<?php

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
