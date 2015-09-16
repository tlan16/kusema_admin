<?php
require_once dirname(__FILE__) . '/bootstrap.php';
Core::setUser(UserAccount::get(UserAccount::ID_SYSTEM_ACCOUNT));

try {
	$transStarted = false;
	try {Dao::beginTransaction();} catch(Exception $e) {$transStarted = true;}

	$connector = QuestionConnector::getConnector(
			ForumConnector::CONNECTOR_TYPE_QUESTION
			,SystemSettings::getByType(SystemSettings::TYPE_FORUM_API_REST)
			, SystemSettings::getByType(SystemSettings::TYPE_FORUM_API_REST_USERNAME)
			, SystemSettings::getByType(SystemSettings::TYPE_FORUM_API_REST_PASSWORD)
	);
	$objs = $connector->getList();
	echo print_r($objs);
	
	if($transStarted === false)
		Dao::commitTransaction();
} catch (Exception $ex) {
	if($transStarted === false)
			Dao::rollbackTransaction();
	throw $ex;
}