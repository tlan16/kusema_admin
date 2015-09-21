<?php
include_once dirname(__FILE__) . '/syncAbstract.php';
class pullTopic extends syncAbstract
{
	public static function run()
	{
		parent::run();
		TopicConnector::importTopic(array(), true);
	}
}

pullTopic::run();