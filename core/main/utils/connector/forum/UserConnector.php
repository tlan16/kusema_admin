<?php
class UserConnector extends ForumConnector
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
	/**
	 * import external system User into system
	 * 
	 * @param array	 $existing
	 * @param bool	 $debug
	 * 
	 * @throws Exception
	 */
	public static function importUser($existing = array(), $debug = false)
	{
		$connector = self::getConnector(
				ForumConnector::CONNECTOR_TYPE_USER
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
				$username = self::processField($obj, 'authcate');
				$email = self::processField($obj, 'email');
				if(($email = trim($email)) === '')
					$email = null;
				$type = self::processField($obj, 'type');
				$isAdmin = self::processField($obj, 'isAdmin', false);
				$lastName = self::processField($obj, 'surname');
				$firstName = self::processField($obj, 'firstName');
				if(($lastName = trim($lastName)) === '' || ($firstName = trim($firstName)) === '')
				{
					if($connector->debug === true)
						echo 'invalid firstname or lastname passed in, user [' . $refId . '] skipped' . PHP_EOL;
					continue;
				}
				if(($username = trim($username)) === '')
				{
					if($connector->debug === true)
						echo 'invalid username passed in, user [' . $refId . '] skipped' . PHP_EOL;
					continue;
				}
				$topics = self::processField($obj, 'topics', array());
				
				$transStarted = false;
				try {Dao::beginTransaction();} catch(Exception $e) {$transStarted = true;}
				
				$systemTopics = array();
				foreach ($topics as $topicId)
				{
					$systemTopics[] = TopicConnector::getTopicById($topicId, $debug);
				}
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
					$msg .= PHP_EOL;
					echo $msg;
				}
				$systemPerson = Person::create($firstName, $lastName, $email);
				if($connector->debug === true)
					echo 'Person[' . $systemPerson->getId() . '] created/updated with firstName "' . $systemPerson->getFirstName() . '", lastName"' . $systemPerson->getLastName() . '"' . PHP_EOL;
				$systemUserAccount = UserAccount::create($username, 'disabled', $systemPerson, Role::get(Role::ID_FORUM_USER));
				if($connector->debug === true)
					echo 'UserAccount[' . $systemUserAccount->getId() . '] created/updated with firstName "' . $systemUserAccount->getUserName() . '", Person[' . $systemUserAccount->getPerson()->getId() . '], Role "' . Role::get(Role::ID_FORUM_USER)->getName() . '"' . PHP_EOL;
				
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