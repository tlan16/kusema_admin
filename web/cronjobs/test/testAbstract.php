<?php
/**
 * Test Abstract
 * 
 * @author Frank-Desktop
 *
 */
abstract class testAbstract
{
	public static function run()
	{
		require_once dirname(__FILE__) . '/../../bootstrap.php';
		Core::setUser(UserAccount::get(UserAccount::ID_SYSTEM_ACCOUNT));
		echo 'START ' . get_called_class() . ' at ' . UDate::now(UDate::TIME_ZONE_MELB) . PHP_EOL;
	}
	public static function getRealJson($json)
	{
		return json_decode(json_encode($json), true);
	}
	public static function getTimeString()
	{
		$obj = UDate::now(UDate::TIME_ZONE_MELB);
		return $obj->format('s_i_h_d_M');
	}
	public static function echoTotalNumber($class) {
		echo 'there are total of ' . count($class::getAll()) . ' ' . $class . PHP_EOL;
	}
}