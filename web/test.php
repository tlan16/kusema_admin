<?php
require_once dirname(__FILE__) . '/bootstrap.php';
Core::setUser(UserAccount::get(UserAccount::ID_SYSTEM_ACCOUNT));

try {
	$transStarted = false;
	try {Dao::beginTransaction();} catch(Exception $e) {$transStarted = true;}

	$objs = Question::getAllByCriteria('refId is NULL or refId = ?', array(""));
	
	$count = 0;
	foreach ($objs as $obj)
	{
		QuestionConnector::sync($obj);
		$count++;
	}
	
	if($transStarted === false)
		Dao::commitTransaction();
} catch (Exception $ex) {
	if($transStarted === false)
			Dao::rollbackTransaction();
	throw $ex;
}