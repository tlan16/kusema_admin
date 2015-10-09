<?php
include_once dirname(__FILE__) . '/syncAbstract.php';
class pullUnit extends syncAbstract
{
	public static function run()
	{
		parent::run();
		UnitConnector::import(array(), true);
	}
}

pullUnit::run();