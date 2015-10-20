<?php
class UnitConnector extends ForumConnector
{
	/**
	 * get the base url
	 */
	private function getBaseUrl()
	{
		return $this->_rest . 'groups';
	}
	/**
	 * get unit list from external system
	 * 
	 * @param array $attributes
	 * 
	 * @return array
	 */
	public function getList($attributes = array(), $posturl = '')
	{
		$result = array();
		$url = ($this->getBaseUrl() . trim($posturl));
		$result = $this->getData($url, $attributes);
		return $result;
	}
	public static function sync(Unit $unit, $debug = false)
	{
		$response = array();
		$connector = self::getConnector(
				ForumConnector::CONNECTOR_TYPE_UNIT
				,SystemSettings::getByType(SystemSettings::TYPE_FORUM_API_REST)
				, SystemSettings::getByType(SystemSettings::TYPE_FORUM_API_REST_USERNAME)
				, SystemSettings::getByType(SystemSettings::TYPE_FORUM_API_REST_PASSWORD)
				, $debug
				);
	
		$array = array(
			'title' => $unit->getName()
			,'unitCode' => $unit->getCode()
			,'name' => $unit->getCode() . ': ' . $unit->getName()
			,'deleted' => !($unit->getActive())
			,'topics' => array()
		);
		
		foreach ($unit->getTopics() as $topic)
		{
			if($topic->getRefId() !== null && $topic->getRefId() !== '')
			{
				$array['topics'][] = $topic->getRefId();
			}
		}
		
		if(trim($unit->getRefId()) !== '')
		{
			$url = $connector->getBaseUrl() . '/' . $unit->getRefId();
			$response = json_decode(ComScriptCURL::readUrl($url, null, $array, "PUT"), true);
		} else {
			$user = Core::getUser();
			if(trim($user->getRefId()) !== '')
				PersonConnector::createByUserAccount($user);
			$authorId = trim($user->getRefId());
			if($authorId === '')
				throw new Exception('Error connecting to remote system');
	
			$array['author'] = $authorId;
			$url = $connector->getBaseUrl();
			$response = json_decode(ComScriptCURL::readUrl($url, null, $array, "POST"), true);
		}
		return $response;
	}
	public static function create(Unit $unit, $debug = false)
	{
		$connector = self::getConnector(
				ForumConnector::CONNECTOR_TYPE_UNIT
				,SystemSettings::getByType(SystemSettings::TYPE_FORUM_API_REST)
				, SystemSettings::getByType(SystemSettings::TYPE_FORUM_API_REST_USERNAME)
				, SystemSettings::getByType(SystemSettings::TYPE_FORUM_API_REST_PASSWORD)
				, $debug
				);
		if(self::getById($unit->getCode()) instanceof Unit)
			return self::getById($unit->getCode());
		$array = array(
			'title' => $unit->getName()
			,'unitCode' => $unit->getCode()
			,'name' => $unit->getCode() . ': ' . $unit->getName()
			,'deleted' => $unit->getActive()
		);
		$response = json_decode(ComScriptCURL::readUrl($connector->getBaseUrl(), null, $array), true);
		if(isset($response['_id']) && trim($response['_id']) !== '')
		{		
			try {
				$transStarted = false;
				try {Dao::beginTransaction();} catch(Exception $e) {$transStarted = true;}
				
				$unit->setRefId($response['_id'])->save();
				
				if($transStarted === false)
					Dao::commitTransaction();
			} catch (Exception $ex) {
				if($transStarted === false)
					Dao::rollbackTransaction();
				throw $ex;
			}
		}
		return  $unit;
	}
	public static function getById($id, $debug = false)
	{
		if(($obj = Unit::getByRefId($id)) instanceof Unit)
			return $obj;
		$connector = self::getConnector(
				ForumConnector::CONNECTOR_TYPE_UNIT
				,SystemSettings::getByType(SystemSettings::TYPE_FORUM_API_REST)
				, SystemSettings::getByType(SystemSettings::TYPE_FORUM_API_REST_USERNAME)
				, SystemSettings::getByType(SystemSettings::TYPE_FORUM_API_REST_PASSWORD)
				, $debug
				);
		$objs = $connector->getList(array(), '?&limit=1&conditions=' . json_encode(array('_id' => urlencode(trim($id)))));
		if(!is_array($objs) || count($objs) === 0)
			return null;
		self::import($objs, $debug);
		return ( Unit::getByRefId($id, false) );
	}
	/**
	 * import external system Units into system
	 * 
	 * @param array	 $existing
	 * @param bool	 $debug
	 * 
	 * @throws Exception
	 */
	public static function import($existing = array(), $debug = false)
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
				
				$unit =Unit::create($name, $code, $refId, null, $active);
				$unit->clearTopics();
				foreach ($topics as $topiRefcId)
				{
					$topic = TopicConnector::getById($topiRefcId, $debug);
					if(!$topic instanceof Topic)
						continue;
					$unit->addTopic($topic);
				}
				if($connector->debug === true)
					echo 'Unit[' . $unit->getId() . '] created/updated with name "' . $unit->getName() . '", code"' . $code . '", refId"' . $unit->getRefId() . '", active"' . intval($unit->getActive()) . '"' . PHP_EOL;
				
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