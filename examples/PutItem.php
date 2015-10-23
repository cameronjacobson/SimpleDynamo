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
  $db->PutItem('sampletable')
    ->addNames(array(
      '#A'=>'a',
      '#C'=>'c'
    ))
    ->addValues(array(
      ':val1'=>1,
      ':val2'=>3,
    ))
    ->conditions(function(){
      return $this->__and(array(
        '#A = :val1',
        '#C = :val2'
      ));
    })
    ->addItem(array(
      'id'=>'abc',
      'a'=>'4',
      'c'=>'5'
    ))
    ->consumed('TOTAL')
    ->metrics('SIZE')
    ->setReturnValues('ALL_OLD')
    ->getResults()
);

var_dump($db->get('abc'));

//$db->delete('abc');
