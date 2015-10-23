<?php

require_once(dirname(__DIR__).'/vendor/autoload.php');

use SimpleDynamo\SimpleDynamo;

if(empty($argv[1])){
	die('Usage: php Query.php [tablename]'.PHP_EOL);
}

$db = new SimpleDynamo(array(
	'profile'=>'dynamo',
	'key'=>'id1',
	'table'=>$argv[1],
	'region'=>'us-west-2',
	'error'=>function($result){
		var_dump($result->getMessage());
	}
));

$db->set(42,array(
	'id2'=>"this is a test",
	'a'=>1,
	'b'=>2,
	'c'=>3
));

var_dump(
  $db->Query($argv[1])
    ->consistent()
    ->addNames(array(
      '#A'=>'a',
      '#B'=>'b',
      '#ID1'=>'id1',
      '#ID2'=>'id2'
    ))
    ->addValues(array(
      ':val1'=>1,
      ':val2'=>3,
      ':val3'=>42,
      ':val4'=>"this is a test"
    ))
    ->expressions(function(){
      return $this->__and(array(
        '#A = :val1',
        '#B < :val2',
      ));
    })
/*
    ->startKey(array(
      'id1'=>42,
      'id2'=>"this is a test"
    ))*/
    //->setIndex('id1')
    ->conditions(function(){
      return $this->__and(array(
        '#ID1 = :val3',
        '#ID2 = :val4'
      ));
    })
    ->limit(1)
    ->projection(array(
        'a','c',
    ))
    ->consumed('TOTAL')
    ->desc()
    ->specific() // OR ->projected OR ->count OR ->all()
    ->getResults()
);

$db->delete(array(
	'id1'=>42,
	'id2'=>"this is a test"
));
