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

$res = $db->query('sampletable')
	->addNames(array(
		'#A' => 'aaa',
		'#index' => 'id'
	))
	->addValues(array(
		':val' => 1,
		':id' => 'abc'
	))
	->consistent()
	->rawConstraint('#index = :id')
	->rawFilter('#A = :val')
	->limit(1)
	->getResults();

var_dump($res);

$db->delete('abc');
