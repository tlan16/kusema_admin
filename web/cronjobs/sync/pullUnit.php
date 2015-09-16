<?php
include_once dirname(__FILE__) . '/syncAbstract.php';
class pullUnit extends syncAbstract
{
	public static function run()
	{
		parent::run();
		UnitConnector::importUnit(array(), true);
	}
}

pullUnit::run();