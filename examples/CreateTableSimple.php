<?php

require_once(dirname(__DIR__).'/vendor/autoload.php');

use SimpleDynamo\SimpleDynamo;

$db = new SimpleDynamo(array(
	'profile'=>'dynamo',
	'key'=>'id',
	'table'=>'sampletable',
	'region'=>'us-west-2',
	'error'=>function($result){
		var_dump($result->getMessage());
	}
));

var_dump(
$db->CreateTable('simpletable'.time())
  ->addAttributes(array(
     'id1'=>'N',
     'id2'=>'S',
  ))
  ->addSchema(array(
    'id1'=>'HASH',
    'id2'=>'RANGE'
  ))
  ->throughput(1,1)
  ->getResults()
);

