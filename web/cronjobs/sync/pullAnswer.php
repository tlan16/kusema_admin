<?php
include_once dirname(__FILE__) . '/syncAbstract.php';
class pullAnswer extends syncAbstract
{
	public static function run()
	{
		parent::run();
		AnswerConnector::import(array(), true);
	}
}

pullAnswer::run();