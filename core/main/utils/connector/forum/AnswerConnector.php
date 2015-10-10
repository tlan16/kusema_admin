<?php
class AnswerConnector extends ForumConnector
{
	/**
	 * get the base url
	 */
	private function getBaseUrl()
	{
		return $this->_rest . 'answers';
	}
	public function getList($attributes = array(), $posturl = '')
	{
		$result = array();
		$url = ($this->getBaseUrl() . trim($posturl));
		$result = $this->getData($url, $attributes);
		return $result;
	}
	public static function sync(Question $question, $debug = false)
	{
		$response = array();
		$connector = self::getConnector(
				ForumConnector::CONNECTOR_TYPE_ANSWER
				,SystemSettings::getByType(SystemSettings::TYPE_FORUM_API_REST)
				, SystemSettings::getByType(SystemSettings::TYPE_FORUM_API_REST_USERNAME)
				, SystemSettings::getByType(SystemSettings::TYPE_FORUM_API_REST_PASSWORD)
				, $debug
				);
	
		if($question->getRefId() !== null && $question->getRefId() !== '')
		{
			$array = array(
					'group' => trim( (count($units = $question->getUnits()) > 0 ? $units[0]->getRefId() : "") )
					,'message' => $question->getContent()
					,'title' => $question->getTitle()
					,'deleted' => !($question->getActive())
// 					,'comments' => array()
					,'anonymous' => !( trim($question->getAuthorName()) === '' )
// 					,'answers' => array()
// 					,'topics' => array()
			);
			$url = $connector->getBaseUrl() . '/' . $question->getRefId();
			$response = json_decode(ComScriptCURL::readUrl($url, null, $array, "PUT"), true);
		}
		return $response;
	}
	public static function getById($id, $debug = false)
	{
		if(($obj = Answer::getByRefId($id)) instanceof Answer)
			return $obj;
		$connector = self::getConnector(
				ForumConnector::CONNECTOR_TYPE_ANSWER
				,SystemSettings::getByType(SystemSettings::TYPE_FORUM_API_REST)
				, SystemSettings::getByType(SystemSettings::TYPE_FORUM_API_REST_USERNAME)
				, SystemSettings::getByType(SystemSettings::TYPE_FORUM_API_REST_PASSWORD)
				, $debug
				);
		$objs = $connector->getList(array(), '?&limit=1&conditions=' . json_encode(array('_id' => urlencode(trim($id)))));
		if(!is_array($objs) || count($objs) === 0)
			return null;
		self::import($objs, $debug);
		return ( Answer::getByRefId($id, false) );
	}
	public static function import($existing = array(), $debug = false)
	{
		$connector = self::getConnector(
				ForumConnector::CONNECTOR_TYPE_ANSWER
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
				if(trim($refId) === '')
					continue;
				$version = $obj["__v"];
				$author = self::processField($obj, 'author');
				$question = self::processField($obj, 'question');
				$content = self::processField($obj, 'message');
				$active = !(self::processField($obj, 'deleted', false));
				$upVotes = self::processField($obj, 'upVotes', array());
				$downVotes = self::processField($obj, 'downVotes', array());
				$anonymous = self::processField($obj, 'anonymous',false);
				$authorName = self::processField($obj, 'authorName', ($anonymous === true ? 'anonymous' : '') );
				
				if(trim($author) === '')
				{
					if($connector->debug === true)
					{
						echo 'invalid author passed in, answer [' . $refId . '] skipped' . PHP_EOL;
						echo print_r($obj, true);
					}
					continue;
				}
				if(trim($question) === '')
				{
					if($connector->debug === true)
					{
						echo 'invalid question id passed in, answer [' . $refId . '] skipped' . PHP_EOL;
						echo print_r($obj, true);
					}
					continue;
				}
				if(trim($content) === '')
				{
					if($connector->debug === true)
					{
						echo 'invalid content(message) passed in, answer [' . $refId . '] skipped' . PHP_EOL;
						echo print_r($obj, true);
					}
					continue;
				}
				
				if($connector->debug === true)
				{
					$msg =  $rowCount . ': user data from forum' . PHP_EOL;
					$msg .= "\t refId(_id) => " . $refId . PHP_EOL;
					$msg .= "\t version(__v) => " . $version . PHP_EOL;
					$msg .= "\t question => " . trim($question) . PHP_EOL;
					$msg .= "\t author => " . $author . PHP_EOL;
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
				
				$questionRefId = $question;
				$question = QuestionConnector::getById($questionRefId, $debug);
				
				if(!$question instanceof Question)
					continue;
				
				$authorRefId = $author;
				$author = PersonConnector::getById($authorRefId, $debug);
				if(!$author instanceof Person)
					continue;
				
				$answer = $question->addAnswer("", $content, $refId, $author, $authorName, $active);
				if($connector->debug === true)
					echo 'Answer[' . $question->getId() . '] created/updated with title "' . $answer->getTitle() . '", content"' . $answer->getContent() . '"' . PHP_EOL;
				
				if(is_array($upVotes))
				{
					foreach ($upVotes as $upVote)
					{
						$person = PersonConnector::getById($upVote, $debug);
						if(!$person instanceof Person)
							continue;
						$answer->voteUp($person);
					}
				}
				foreach ($downVotes as $downVote)
				{
					$person = PersonConnector::getById($downVote);
					if(!$person instanceof Person)
						continue;
					$answer->voteDown($person);
				}
				
				if($transStarted === false)
				{
					$rowCount++;
					Dao::commitTransaction();
				} else {
					if($debug === true)
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