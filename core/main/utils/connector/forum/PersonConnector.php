<?php
class PersonConnector extends ForumConnector
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
		$url = $this->_rest . 'users';
		$result = $this->getData($url, $attributes);
		return $result;
	}
	public static function getPersonById($id, $debug = false)
	{
		$connector = self::getConnector(
				ForumConnector::CONNECTOR_TYPE_PERSON
				,SystemSettings::getByType(SystemSettings::TYPE_FORUM_API_REST)
				, SystemSettings::getByType(SystemSettings::TYPE_FORUM_API_REST_USERNAME)
				, SystemSettings::getByType(SystemSettings::TYPE_FORUM_API_REST_PASSWORD)
				, $debug
				);
		$objs = $connector->getList(array(), '?&limit=1&conditions=' . json_encode(array('_id' => urlencode(trim($id)))));
		if(!is_array($objs) || count($objs) === 0)
			return null;
		self::importPerson($objs, $debug);
		return ( Person::getByRefId($id, false) );
	}
	/**
	 * import external system Person into system
	 * 
	 * @param array	 $existing
	 * @param bool	 $debug
	 * 
	 * @throws Exception
	 */
	public static function importPerson($existing = array(), $debug = false)
	{
		$connector = self::getConnector(
				ForumConnector::CONNECTOR_TYPE_PERSON
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
				$refId = $obj["_id"];
				$version = $obj["__v"];
				$username = self::processField($obj, 'username');
				$email = self::processField($obj, 'email');
				if(($email = trim($email)) === '')
					$email = null;
				$type = self::processField($obj, 'type');
				$isAdmin = self::processField($obj, 'isAdmin', false);
				$firstName = self::processField($obj, 'firstName');
				$firstName = (trim($firstName) === "" ? trim($username) : trim($firstName));
				$lastName = self::processField($obj, 'surname');
				if($firstName === '')
				{
					if($connector->debug === true)
						echo 'invalid firstname "' . $firstName . '" passed in, user [' . $refId . '] skipped' . PHP_EOL;
						echo print_r($obj, true);
					continue;
				}
				$defaultArray = array(
						'topics' => array()
						,'groups' => array()
				);
				$authcateSubscriptions = self::processField($obj, 'authcateSubscriptions', $defaultArray);
				$authcateSubscriptionsTopics = self::processField($authcateSubscriptions, 'topics', array());
				$authcateSubscriptionsUnits = self::processField($authcateSubscriptions, 'groups', array());
				
				$manualSubscriptions = self::processField($obj, 'manualSubscriptions', $defaultArray);
				$manualSubscriptionsTopics = self::processField($manualSubscriptions, 'topics', array());
				$manualSubscriptionsUnits = self::processField($manualSubscriptions, 'groups', array());
				
				$transStarted = false;
				try {Dao::beginTransaction();} catch(Exception $e) {$transStarted = true;}
				
				if($connector->debug === true)
				{
					$msg =  $rowCount . ': user data from forum' . PHP_EOL;
					$msg .= "\t refId(_id) => " . $refId . PHP_EOL; 
					$msg .= "\t version(__v) => " . $version . PHP_EOL; 
					$msg .= "\t firstName => " . $firstName . PHP_EOL;
					$msg .= "\t lastName => " . $lastName . PHP_EOL;
					$msg .= "\t username(authcate) => " . $username . PHP_EOL;
					$msg .= "\t email => " . trim($email) . PHP_EOL;
					$msg .= "\t isAdmin => " . trim($isAdmin) . PHP_EOL;
					$msg .= "\t type => " . $type . PHP_EOL;
					$msg .= "\t subscribedTopics(manualSubscriptions[topics]) => " . print_r($manualSubscriptionsTopics,true) . PHP_EOL;
					$msg .= "\t subscribedUnits(manualSubscriptions[groups]) => " . print_r($manualSubscriptionsUnits,true) . PHP_EOL;
					$msg .= "\t enrolledTopics(authcateSubscriptions[topics]) => " . print_r($authcateSubscriptionsTopics,true) . PHP_EOL;
					$msg .= "\t enrolledUnits(authcateSubscriptions[groups]) => " . print_r($authcateSubscriptionsUnits,true) . PHP_EOL;
					$msg .= PHP_EOL;
					echo $msg;
				}
				$systemPerson = Person::create($firstName, $lastName, $email, $refId);
				if($connector->debug === true)
					echo 'Person[' . $systemPerson->getId() . '] created/updated with firstName "' . $systemPerson->getFirstName() . '", lastName"' . $systemPerson->getLastName() . '", refId"' . $systemPerson->getRefId() . '"' . PHP_EOL;
				
				foreach ($manualSubscriptionsTopics as $topicRefId)
				{
					if(!($topic = Topic::getByRefId($topicRefId)) instanceof Topic)
						$topic = TopicConnector::getTopicById($topicRefId);
					$systemPerson->subscribeTopic($topic);
				}
				foreach ($manualSubscriptionsUnits as $unitRefId)
				{
					if(!($unit = Unit::getByRefId($unitRefId)) instanceof Unit)
						$unit = UnitConnector::getUnitById($unitRefId);
					$systemPerson->subscribeUnit($unit);
				}
				foreach ($authcateSubscriptionsTopics as $topicRefId)
				{
					if(!($topic = Topic::getByRefId($topicRefId)) instanceof Topic)
						$topic = TopicConnector::getTopicById($topicRefId);
					$systemPerson->enrollTopic($topic);
				}
				foreach ($authcateSubscriptionsUnits as $unitRefId)
				{
					if(!($unit = Unit::getByRefId($unitRefId)) instanceof Unit)
						$unit = UnitConnector::getUnitById($unitRefId);
					$systemPerson->enrollUnit($unit);
				}
					
				if($transStarted === false)
				{
					$rowCount++;
					Dao::commitTransaction();
				} else {
					if($connector->debug === true)
						echo '***warning*** $transStarted !== false' . PHP_EOL;
				};
			} catch (Exception $ex) {
				if($transStarted === false)
					Dao::rollbackTransaction();
				throw $ex;
			}
		}
	}
}