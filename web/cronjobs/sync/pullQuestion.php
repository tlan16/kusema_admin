<?php
include_once dirname(__FILE__) . '/syncAbstract.php';
class pullQuestion extends syncAbstract
{
	public static function run()
	{
		parent::run();
		QuestionConnector::import(array(), true);
	}
}

pullQuestion::run();