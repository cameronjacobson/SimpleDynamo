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
  $db->UpdateItem('sampletable')
    ->addKeys(array(
		'id1'=>42,
		'id2'=>'abc'
	))
    ->addNames(array(
      '#A'=>'a',
      '#B'=>'b'
    ))
    ->addValues(array(
      ':val1'=>'1',
      ':val2'=>'2'
    ))
	->conditions(function(){
		return $this->__and(
			'#A = :val1',
			'#B = :val2'
		);
	})
	->updates(function(){
		$this->_add_(array(
			
		));
		$this->_set_(array(
			
		));
	})
	->setReturnValues('ALL_OLD')
    ->consumed('TOTAL')
	->metrics('SIZE')
    ->getResults()
);

$db->delete('abc');
