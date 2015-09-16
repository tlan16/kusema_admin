<?php
abstract class syncAbstract
{
	public static function run()
	{
		require_once dirname(__FILE__) . '/../../bootstrap.php';
		Core::setUser(UserAccount::get(UserAccount::ID_SYSTEM_ACCOUNT));
		echo 'START ' . get_called_class() . ' at ' . UDate::now(UDate::TIME_ZONE_MELB) . PHP_EOL;
	}
}