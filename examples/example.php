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

// SET VALUE
$db->set('abc',array('a'=>1,'b'=>2,'c'=>3,'d'=>'123'));

// GET VALUE
var_dump(
	$db->get('abc')
);

// DELETE VALUE
$db->delete('abc');
