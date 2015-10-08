<?php
class QuestionConnector extends ForumConnector
{
	public function getList($attributes = array())
	{
		$result = array();
		$url = $this->_rest . 'questions';
		echo $url . PHP_EOL;
		$extraAttributes = array();
		foreach ($extraAttributes as $value)
			self::addExtraAttribute($attributes, 'path', $value);
		$result = $this->getData($url, $attributes);
		return $result;
	}
	public static function importQuestion($existing = array(), $debug = false)
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
				if(!($author = Person::getByRefId($authorRefId)) instanceof Person)
					$author = PersonConnector::getPersonById($authorRefId);
				$question = Question::create($title, $content, $refId, $author, $authorName, $active);
				foreach ($upVotes as $upVote)
				{
					if(!($person = Person::getByRefId($upVote)) instanceof Person)
						$person = PersonConnector::getPersonById($upVote);
					var_dump($person);
					$question->voteUp($person);
				}
				foreach ($downVotes as $downVote)
				{
					if(!($person = Person::getByRefId($downVote)) instanceof Person)
						$person = PersonConnector::getPersonById($downVote);
					$question->voteDown($person);
				}
				if(($unit = trim($unit)) !== '')
				{
					$unitRefId = $unit;
					if(!($unit = Unit::getByRefId($unitRefId)) instanceof Unit)
						$unit = UnitConnector::getUnitById($unitRefId);
					$question->addUnit($unit);
				}
				foreach ($topics as $topic)
				{
					if(($topicRefId = trim($topic)) !== '')
					{
						if(!($topic = Topic::getByRefId($topicRefId)) instanceof Topic)
							$topic = TopicConnector::getTopicById($topicRefId);
						$question->addTopic($topic);
					}
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