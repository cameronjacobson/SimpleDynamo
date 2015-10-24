<?php

require_once(dirname(__DIR__).'/vendor/autoload.php');

use SimpleDynamo\SimpleDynamo;

$db = new SimpleDynamo(array(
	'profile'=>'dynamo',
	'key'=>'id1',
	'table'=>'mytabletest',
	'region'=>'us-west-2',
	'error'=>function($result){
		var_dump($result->getMessage());
	}
));

$db->set(42,array(
	'id2'=>'abc',
	'a'=>1,
	'b'=>2,
	'c'=>3
));

var_dump(
  $db->Scan('mytabletest')
    ->consistent()
    ->addNames(array(
      '#A'=>'a',
      '#B'=>'b'
    ))
    ->addValues(array(
      ':val1'=>1,
      ':val2'=>3
    ))
    //->startKey(array('id1'=>41,'id2'=>'aaa'))
    //->setIndex('id1')
    ->filters(function(){
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
    ->specific() // OR projected() OR all() OR ->count
    ->segment(1,2)
    ->getResults()
);

$db->delete(array(
	'id1'=>42,
	'id2'=>'abc'
));
