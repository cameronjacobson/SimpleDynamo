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

var_dump(
$db->CreateTable('mytabletest'.time())
  ->addAttributes(array(
     'id1'=>'N',
     'id2'=>'S',
     'global0'=>'N',
     'global1'=>'N',
     'local1'=>'N',
     'local2'=>'N'
  ))
  ->addGlobal('globalindex',function(){
    $this->addKeys(array(
      'global0'=>'HASH',
      'global1'=>'RANGE'
    ));
    $this->projection(array(
      'col1',
      'col2'
    ));
    $this->throughput(1,1);
  })
  ->addSchema(array(
    'id1'=>'HASH',
    'id2'=>'RANGE'
  ))
  ->addLocal('localindex',function(){
    $this->addKeys(array(
      'id1'=>'HASH',
      'local1'=>'RANGE'
    ));
    $this->projection('ALL');
  })
  ->addLocal('localindex2',function(){
    $this->addKeys(array(
      'id1'=>'HASH',
      'local2'=>'RANGE'
    ));
    $this->projection('KEYS_ONLY');
  })
  ->throughput(1,1)
  ->stream('NEW_AND_OLD_IMAGES')
  ->getResults()
);

