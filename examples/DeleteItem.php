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

$db->set('blah',array(
	'ggg'=>11,
	'hhh'=>2
));

var_dump(
$db->DeleteItem('sampletable')
  ->addKey('id','blah')
  ->addNames(array(
    '#A'=>'ggg',
    '#B'=>'hhh'
  ))
  ->addValues(array(
    ':val1'=>11,
    ':val2'=>10
  ))
  ->conditional(function(){
    return $this->__and(array(
      '#A = :val1',
      '#B < :val2'
    ));
  })
  ->consumed('TOTAL')
  ->metrics('SIZE')
  ->setReturnValues('ALL_OLD')
  ->getResults()
);

var_dump($db->get('blah'));

$db->delete('blah');
