<?php
require_once dirname(__FILE__) . '/bootstrap.php';
Core::setUser(UserAccount::get(UserAccount::ID_SYSTEM_ACCOUNT));

try {
	$transStarted = false;
	try {Dao::beginTransaction();} catch(Exception $e) {$transStarted = true;}

	$question = Question::get(1);
	$question->voteUp(Core::getUser()->getPerson());
	$question->voteUp(Core::getUser()->getPerson());
	$question->voteUp(Core::getUser()->getPerson());
	$question->voteDown(Core::getUser()->getPerson());
	
	if($transStarted === false)
		Dao::commitTransaction();
} catch (Exception $ex) {
	if($transStarted === false)
			Dao::rollbackTransaction();
	throw $ex;
}