<?php
include_once dirname(__FILE__) . '/syncAbstract.php';
class pullUser extends syncAbstract
{
	public static function run()
	{
		parent::run();
		PersonConnector::import(array(), true);
	}
}

pullUser::run();