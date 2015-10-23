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
  $db->Scan('sampletable')
    ->consistent()
    ->addNames(array(
      '#A'=>'a',
      '#B'=>'b'
    ))
    ->addValues(array(
      ':index'=>'abc',
      ':val1'=>1,
      ':val2'=>3
    ))
    ->expressions(function(){
      return $this->__and(array(
        'id = :index',
      ));
    })
    ->startKey('id','abb')
    ->setIndex('id')
    ->conditions(function(){
      return $this->__and(array(
        '#A = :val1',
        '#B < :val2'
      ));
    })
    ->limit(1)
    ->projection(array(
        'a','c',
    ))
    ->consumed('TOTAL')
    ->desc()
    ->projected() // OR all() OR ->count
    ->segment(1,1)
    ->getResults()
);

$db->delete('abc');
