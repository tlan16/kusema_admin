<?php
include_once dirname(__FILE__) . '/syncAbstract.php';
class pullTopic extends syncAbstract
{
	public static function run()
	{
		parent::run();
		TopicConnector::importTopic(array(), true);
// 		var_dump(TopicConnector::getTopic("Chemical Engineering",true));
	}
}

pullTopic::run();