<?php
include_once dirname(__FILE__) . '/syncAbstract.php';
class pullComments extends syncAbstract
{
	public static function run()
	{
		parent::run();
		CommentsConnector::import(array(), true);
	}
}

pullComments::run();