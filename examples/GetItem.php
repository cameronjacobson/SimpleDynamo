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

$db->set('abc',array(
	'a'=>1,
	'b'=>2,
	'c'=>3
));

var_dump(
  $db->GetItem('sampletable')
    ->consistent()
    ->addNames(array(
      '#A'=>'a',
      '#B'=>'b'
    ))
    ->addKey('id','abc')
    ->projection(array(
      '#A',
      '#B'
    ))
    ->consumed('TOTAL')
    ->getResults()
);

$db->delete('abc');
