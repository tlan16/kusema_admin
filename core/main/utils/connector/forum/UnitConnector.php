<?php
class UnitConnector extends ForumConnector
{
	/**
	 * get unit list from external system
	 * 
	 * @param array $attributes
	 * 
	 * @return array
	 */
	public function getList($attributes = array())
	{
		$result = array();
		$url = $this->_rest . 'groups';
		$result = $this->getData($url, $attributes);
		return $result;
	}
	/**
	 * import external system Units into system
	 * 
	 * @param array	 $existing
	 * @param bool	 $debug
	 * 
	 * @throws Exception
	 */
	public static function importUnit($existing = array(), $debug = false)
	{
		$connector = self::getConnector(
				ForumConnector::CONNECTOR_TYPE_UNIT
				,SystemSettings::getByType(SystemSettings::TYPE_FORUM_API_REST)
				, SystemSettings::getByType(SystemSettings::TYPE_FORUM_API_REST_USERNAME)
				, SystemSettings::getByType(SystemSettings::TYPE_FORUM_API_REST_PASSWORD)
				, $debug
		);
		$objs = (count($existing) === 0 ? $connector->getList() : $existing);
		$rowCount = 0;
		foreach ($objs as $obj)
		{
			try {
				$transStarted = false;
				try {Dao::beginTransaction();} catch(Exception $e) {$transStarted = true;}
				
				$refId = $obj["_id"];
				$version = $obj["__v"];
				$name = self::processField($obj, 'title');
				$code = self::processField($obj, 'unitCode');
				$active = (isset($obj["deleted"]) ? (intval(trim($obj["deleted"])) === 0) : false);
				
				$topics = self::processField($obj, 'topics', array());
				$systemTopics = array();
				foreach ($topics as $topicId)
				{
					$systemTopics[] = TopicConnector::getTopicById($topicId, $debug);
				}
				if($connector->debug === true)
				{
					$msg =  $rowCount . ': topic data from forum' . PHP_EOL;
					$msg .= "\t refId(_id) => " . $refId . PHP_EOL; 
					$msg .= "\t version(__v) => " . $version . PHP_EOL; 
					$msg .= "\t name(title) => " . $name . PHP_EOL;
					$msg .= "\t code(unitCode) => " . $code . PHP_EOL;
					$msg .= "\t topics => " . print_r($topics,true) . PHP_EOL;
					$msg .= "\t active(!deleted) => " . intval($active) . PHP_EOL;
					$msg .= PHP_EOL;
					echo $msg;
				}
				
				$systemObj =Unit::create($name, $code, $refId, null, $active);
				foreach ($systemTopics as $systemTopic)
				{
					if($systemTopic instanceof Topic)
						$systemObj->addTopic($systemTopic);
					if($connector->debug === true)
						echo 'Topic[' . $systemTopic->getId() . '] ' . $systemTopic->getName() . ' is associated with Unit[' . $systemObj->getId() . '] ' . $systemObj->getName() . PHP_EOL;  
				}
				if($connector->debug === true)
					echo 'Unit[' . $systemObj->getId() . '] created/updated with name "' . $systemObj->getName() . '", code"' . $code . '", refId"' . $systemObj->getRefId() . '", active"' . intval($systemObj->getActive()) . '"' . PHP_EOL;
				
				if($transStarted === false)
				{
					$rowCount++;
					Dao::commitTransaction();
				}
			} catch (Exception $ex) {
				if($transStarted === false)
					Dao::rollbackTransaction();
				throw $ex;
			}
		}
	}
}