<?php
require_once dirname(__FILE__) . '/../../bootstrap.php';
Core::setUser(UserAccount::get(UserAccount::ID_SYSTEM_ACCOUNT));
echo 'START ' . basename(__FILE__) . ' at ' . UDate::now(UDate::TIME_ZONE_MELB) . PHP_EOL;

StaticsRunner::runStatics();

echo 'DONE ' . basename(__FILE__) . ' at ' . UDate::now(UDate::TIME_ZONE_MELB) . PHP_EOL;

// function getConfig($type, $entity, $action)
// {
// 	$param = new stdClass();
// 	$param->CallbackParameter = new stdClass();
// 	$param->CallbackParameter->searchCriterias = '';
// 	$param->CallbackParameter->title = '';
// 	$param->CallbackParameter->type = $type;
// 	$param->CallbackParameter->entity = $entity;
// 	$param->CallbackParameter->action = $action;
	
// 	return $param;
// }
// function run() 
// {
// 	$class= new StaticsPageAbstract();
	
// }