<?php
require_once dirname(__FILE__) . '/bootstrap.php';
Core::setUser(UserAccount::get(UserAccount::ID_SYSTEM_ACCOUNT));

try {
	$transStarted = false;
	try {Dao::beginTransaction();} catch(Exception $e) {$transStarted = true;}

	$userAccount = UserAccount::get(10);
	$userAccount->clearRoles()->addRole(Role::get(Role::ID_SYSTEM_ADMIN));
	$userAccount = UserAccount::get(24);
	$userAccount->clearRoles()->addRole(Role::get(Role::ID_SYSTEM_DEVELOPER));
	$userAccount = UserAccount::get(25);
	$userAccount->clearRoles()->addRole(Role::get(Role::ID_ADMIN_USER));
	
	if($transStarted === false)
		Dao::commitTransaction();
} catch (Exception $ex) {
	if($transStarted === false)
			Dao::rollbackTransaction();
	throw $ex;
}