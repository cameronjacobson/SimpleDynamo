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
	'ccc'=>3
));

$res = $db->simplequery('sampletable')
	->addNames(array(
		'#A' => 'aaa',
		'#B' => 'bbb',
		'#index' => 'id'
	))
	->addValues(array(
		':val1' => 1,
		':val2' => 2,
		':id' => 'abc'
	))
	->consistent()
	->rawConstraint('#index = :id')
	->rawFilter('#A = :val1 and #B = :val2')
	->limit(1)
	->getResults();

var_dump($res);

$db->delete('abc');
