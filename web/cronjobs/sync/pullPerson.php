<?php
include_once dirname(__FILE__) . '/syncAbstract.php';
class pullUser extends syncAbstract
{
	public static function run()
	{
		parent::run();
		PersonConnector::importPerson(array(), true);
	}
}

pullUser::run();