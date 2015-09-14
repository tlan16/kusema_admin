<?php
require_once dirname(__FILE__) . '/bootstrap.php';
Core::setUser(UserAccount::get(UserAccount::ID_SYSTEM_ACCOUNT));

try {
	$transStarted = false;
	try {Dao::beginTransaction();} catch(Exception $e) {$transStarted = true;}

	$statics = Question::getDailyCreatedCounts(UDate::now()->modify('-1 year'), UDate::now());
	var_dump($statics);
	
	if($transStarted === false)
		Dao::commitTransaction();
} catch (SoapFault $e) {
	if($transStarted === false)
			Dao::rollbackTransaction();
	throw $ex;
}
