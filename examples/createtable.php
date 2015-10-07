<?php

require_once(dirname(__DIR__).'/vendor/autoload.php');

use SimpleDynamo\SimpleDynamo;

$db = new SimpleDynamo(array(
	'profile'=>'dynamo',
	'key'=>'id',
	'table'=>'sampletable',
	'region'=>'us-west-2',
	'error'=>function($result){
		throw new Exception($result);
	}
));

$db->createTable(array(
	'readcapacity'=>1,
	'writecapacity'=>1,
	'keys'=>array(
		array('id','HASH') // or 'RANGE'
	)
));
