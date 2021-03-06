<?php
class TopicConnector extends ForumConnector
{
	/**
	 * get the base url
	 */
	private function getBaseUrl()
	{
		return $this->_rest . 'topics';
	}
	/**
	 * get topic list from external system
	 * 
	 * @param array 	$attributes
	 * @param string 	$posturl
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
	public static function sync(Topic $topic, $debug = false)
	{
		$response = array();
		$connector = self::getConnector(
				ForumConnector::CONNECTOR_TYPE_TOPIC
				,SystemSettings::getByType(SystemSettings::TYPE_FORUM_API_REST)
				, SystemSettings::getByType(SystemSettings::TYPE_FORUM_API_REST_USERNAME)
				, SystemSettings::getByType(SystemSettings::TYPE_FORUM_API_REST_PASSWORD)
				, $debug
				);
	
		$array = array(
			'name' => $topic->getName()
			,'deleted' => !($topic->getActive())
		);
		
		var_dump(trim($topic->getRefId()));
		if(trim($topic->getRefId()) !== '')
		{
			$url = $connector->getBaseUrl() . '/' . $topic->getRefId();
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
		if(isset($response['_id']) && trim($response['_id']) !== '')
			$topic->setRefId($response['_id'])->save();
		var_dump($response);
		return $response;
	}
	/**
	 * get system Topic by external system topic id
	 * 
	 * @param string $id
	 * @param bool	 $debug
	 * 
	 * @return Topic
	 */
	public static function getById($id, $debug = false)
	{
		if(($obj = Topic::getByRefId($id)) instanceof Topic)
			return $obj;
		$connector = self::getConnector(
				ForumConnector::CONNECTOR_TYPE_TOPIC
				,SystemSettings::getByType(SystemSettings::TYPE_FORUM_API_REST)
				, SystemSettings::getByType(SystemSettings::TYPE_FORUM_API_REST_USERNAME)
				, SystemSettings::getByType(SystemSettings::TYPE_FORUM_API_REST_PASSWORD)
				, $debug
		);
		$objs = $connector->getList(array(), '?&limit=1&conditions=' . json_encode(array('_id' => urlencode(trim($id)))));
		if(!is_array($objs) || count($objs) === 0)
			return null;
		self::importTopic($objs, $debug);
		return ( Topic::getByRefId($id, false) );
	}
	/**
	 * import external system Topics into system
	 * 
	 * @param array	$existing
	 * @param bool	$debug
	 * 
	 * @throws Exception
	 */
	public static function importTopic($existing = array(), $debug = false)
	{
		$connector = self::getConnector(
				ForumConnector::CONNECTOR_TYPE_TOPIC
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
				$name = self::processField($obj, 'name');
				$active = (isset($obj["deleted"]) ? (intval(trim($obj["deleted"])) === 0) : false);
				
				if($connector->debug === true)
				{
					$msg =  $rowCount . ': topic data from forum' . PHP_EOL;
					$msg .= "\t refId(_id) => " . $refId . PHP_EOL; 
					$msg .= "\t version(__v) => " . $version . PHP_EOL; 
					$msg .= "\t name => " . $name . PHP_EOL;
					$msg .= "\t active(!deleted) => " . intval($active) . PHP_EOL;
					$msg .= PHP_EOL;
					echo $msg;
				}
				
				$systemObj =Topic::create($name, $refId, $active);
				if($connector->debug === true)
					echo 'Topic[' . $systemObj->getId() . '] created/updated with name "' . $systemObj->getName() . '", refId "' . $systemObj->getRefId() . '", active "' . intval($systemObj->getActive()) . '"' . PHP_EOL;
				
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