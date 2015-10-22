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

$db->set('abc', array(
	'aaa'=>1,
	'bbb'=>2,
	'ccc'=>3,
));

var_dump(
  $db->BatchGetItem()
	->table('sampletable')
	->consistent()
	->addNames(array(
		'#A'=>'aaa',
		'#B'=>'bbb',
		'#C'=>'ccc',
	))
	->addKeys('id',array(
		'abc'
	))
	->projection('#A, #B, #C')
	->getResults()
);

$db->delete('abc');
