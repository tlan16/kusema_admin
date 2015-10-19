<?php
class QuestionConnector extends ForumConnector
{
	/**
	 * get the base url
	 */
	private function getBaseUrl()
	{
		return $this->_rest . 'questions';
	}
	public function getList($attributes = array(), $posturl = '')
	{
		$result = array();
		$url = ($this->getBaseUrl() . trim($posturl));
		$result = $this->getData($url, $attributes);
		return $result;
	}
	public static function getById($id, $debug = false)
	{
		if(($obj = Question::getByRefId($id)) instanceof Question)
			return $obj;
		$connector = self::getConnector(
				ForumConnector::CONNECTOR_TYPE_QUESTION
				,SystemSettings::getByType(SystemSettings::TYPE_FORUM_API_REST)
				, SystemSettings::getByType(SystemSettings::TYPE_FORUM_API_REST_USERNAME)
				, SystemSettings::getByType(SystemSettings::TYPE_FORUM_API_REST_PASSWORD)
				, $debug
				);
		$objs = $connector->getList(array(), '?&limit=1&conditions=' . json_encode(array('_id' => urlencode(trim($id)))));
		if(!is_array($objs) || count($objs) === 0)
			return null;
		self::import($objs, $debug);
		return ( Question::getByRefId($id, false) );
	}
	public static function sync(Question $question, $debug = false)
	{
		$response = array();
		$connector = self::getConnector(
				ForumConnector::CONNECTOR_TYPE_QUESTION
				,SystemSettings::getByType(SystemSettings::TYPE_FORUM_API_REST)
				, SystemSettings::getByType(SystemSettings::TYPE_FORUM_API_REST_USERNAME)
				, SystemSettings::getByType(SystemSettings::TYPE_FORUM_API_REST_PASSWORD)
				, $debug
				);
	
		if($question->getRefId() !== null && $question->getRefId() !== '')
		{
			$topics = $question->getTopics();
			$topicRefIds = array();
			foreach ($topics as $topic)
			{
				if(($refId = trim($topic->getRefId())) !== '')
					$topicRefIds[] = $refId;
			}
			
			$units = $question->getUnits();
			$unitRefIds= array();
			foreach ($units as $unit)
			{
				if(($refId = trim($unit->getRefId())) !== '')
					$unitRefIds[] = $refId;
			}
			
			$array = array(
					'group' => trim( (count($units = $question->getUnits()) > 0 ? $units[0]->getRefId() : "") )
					,'message' => $question->getContent()
					,'title' => $question->getTitle()
					,'deleted' => !($question->getActive())
					,'anonymous' => !( trim($question->getAuthorName()) === '' )
					,'topics' => $topicRefIds
					,'group' => count($unitRefIds) > 0 ? $unitRefIds[0] : ''
			);
			
			if(($author = $question->getAuthor()) instanceof Person && ($refId = $author->getRefId()) !== '')
				$array['author'] = $refId;
			
			$url = $connector->getBaseUrl() . '/' . $question->getRefId();
			$response = json_decode(ComScriptCURL::readUrl($url, null, $array, "PUT"), true);
		}
		return $response;
	}
	public static function import($existing = array(), $debug = false)
	{
		$connector = self::getConnector(
				ForumConnector::CONNECTOR_TYPE_QUESTION
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
				$author = self::processField($obj, 'author');
				$unit = self::processField($obj, 'group');
				$topics = self::processField($obj, 'topics', array());
				$title = self::processField($obj, 'title');
				$content = self::processField($obj, 'message');
				$active = !(self::processField($obj, 'deleted', false));
				$upVotes = self::processField($obj, 'upVotes', array());
				$downVotes = self::processField($obj, 'downVotes', array());
				$anonymous = self::processField($obj, 'anonymous',false);
				$authorName = self::processField($obj, 'authorName', ($anonymous === true ? 'anonymous' : '') );
				
				if(($title = trim($title)) === '')
				{
					if($connector->debug === true)
					{
						echo 'invalid title passed in, question [' . $refId . '] skipped' . PHP_EOL;
						echo print_r($obj, true);
					}
					continue;
				}
				if(($content = trim($content)) === '')
				{
					if($connector->debug === true)
					{
						echo 'invalid content(message) passed in, question [' . $refId . '] skipped' . PHP_EOL;
						echo print_r($obj, true);
					}
					continue;
				}
				

				if($connector->debug === true)
				{
					$msg =  $rowCount . ': user data from forum' . PHP_EOL;
					$msg .= "\t refId(_id) => " . $refId . PHP_EOL;
					$msg .= "\t version(__v) => " . $version . PHP_EOL;
					$msg .= "\t author => " . $author . PHP_EOL;
					$msg .= "\t unit(group) => " . $unit . PHP_EOL;
					$msg .= "\t topics => " . print_r($topics,true) . PHP_EOL;
					$msg .= "\t title => " . trim($title) . PHP_EOL;
					$msg .= "\t content(message) => " . trim($content) . PHP_EOL;
					$msg .= "\t active(!deleted) => " . trim($active) . PHP_EOL;
					$msg .= "\t upVotes => " . print_r($upVotes,true) . PHP_EOL;
					$msg .= "\t downVotes => " . print_r($downVotes,true) . PHP_EOL;
					$msg .= "\t anonymous => " . trim($anonymous) . PHP_EOL;
					$msg .= "\t authorName => " . trim($authorName) . PHP_EOL;
					$msg .= PHP_EOL;
					echo $msg;
				}
				
				$transStarted = false;
				try {Dao::beginTransaction();} catch(Exception $e) {$transStarted = true;}
				
				$authorRefId = $author;
				if(trim($authorRefId) === '')
				{
					if($connector->debug === true)
						echo 'invalid author given for question ' . $refId . ', skipping this question' . PHP_EOL;
					continue;
				}
				if(!($author = Person::getByRefId($authorRefId)) instanceof Person)
					$author = PersonConnector::getById($authorRefId);
				if(!$author instanceof Person)
					continue;
				$question = Question::create($title, $content, $refId, $author, $authorName, $active);
				foreach ($upVotes as $upVote)
				{
					if(!($person = Person::getByRefId($upVote)) instanceof Person)
						$person = PersonConnector::getById($upVote);
					if(!$person instanceof Person)
						continue;
					$question->voteUp($person);
				}
				foreach ($downVotes as $downVote)
				{
					if(!($person = Person::getByRefId($downVote)) instanceof Person)
						$person = PersonConnector::getById($downVote);
					if(!$person instanceof Person)
						continue;
					$question->voteDown($person);
				}
				if(($unit = trim($unit)) !== '')
				{
					$unitRefId = $unit;
					if(trim($unitRefId) === '')
						continue;
					if(!($unit = Unit::getByRefId($unitRefId)) instanceof Unit)
						$unit = UnitConnector::getById($unitRefId);
					if(!$unit instanceof Unit)
						continue;
					$question->addUnit($unit);
				}
				foreach ($topics as $topic)
				{
					$topicRefId = $topic;
					if(trim($topicRefId) === '')
						continue;
					if(!($topic = Topic::getByRefId($topicRefId)) instanceof Topic)
						$topic = TopicConnector::getById($topicRefId);
					if(!$topic instanceof Topic)
						continue;
					$question->addTopic($topic);
				}
				
				if($connector->debug === true)
					echo 'Question[' . $question->getId() . '] created/updated with title "' . $question->getTitle() . '", content"' . $question->getContent() . '"' . PHP_EOL;

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