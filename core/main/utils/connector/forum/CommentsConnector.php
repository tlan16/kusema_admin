<?php
class CommentsConnector extends ForumConnector
{
	/**
	 * get the base url
	 */
	private function getBaseUrl()
	{
		return $this->_rest . 'comments';
	}
	public function getList($attributes = array(), $posturl = '')
	{
		$result = array();
		$url = ($this->getBaseUrl() . trim($posturl));
		$result = $this->getData($url, $attributes);
		return $result;
	}
	public static function sync(Comments $comments, $debug = false)
	{
		$response = array();
		$connector = self::getConnector(
				ForumConnector::CONNECTOR_TYPE_COMMENTS
				,SystemSettings::getByType(SystemSettings::TYPE_FORUM_API_REST)
				, SystemSettings::getByType(SystemSettings::TYPE_FORUM_API_REST_USERNAME)
				, SystemSettings::getByType(SystemSettings::TYPE_FORUM_API_REST_PASSWORD)
				, $debug
				);
	
		if($comments->getEntityName() !== 'Question' && $comments->getEntityName() !== 'Answer')
			throw new Exception('cannot find valid parent for given Comments');
		if($comments->getEntityName() === 'Question')
		{
			$parent = Question::get($comments->getEntityId());
			var_dump($comments);
			if(!$parent instanceof Question || trim($parent->getRefId()) === '')
				throw new Exception('cannot find valid Question for given Comments');
		}
		if($comments->getEntityName() === 'Answer')
		{
			$parent = Answer::get($comments->getEntityId());
			if(!$parent instanceof Answer || trim($parent->getRefId()) === '')
				throw new Exception('cannot find valid Answer for given Comments');
		}
			
		$array = array(
				'message' => $comments->getContent()
				,'parent' => trim($parent->getRefId())
				,'deleted' => !($comments->getActive())
				,'anonymous' => !( trim($comments->getAuthorName()) === '' )
		);
		if(trim($comments->getRefId()) !== '')
		{
			$url = $connector->getBaseUrl() . '/' . $comments->getRefId();
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
			$comments->setRefId($response['_id'])->save();
		return $response;
	}
	public static function getById($id, $debug = false)
	{
		if(($obj = Answer::getByRefId($id)) instanceof Answer)
			return $obj;
		$connector = self::getConnector(
				ForumConnector::CONNECTOR_TYPE_COMMENTS
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
				ForumConnector::CONNECTOR_TYPE_COMMENTS
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
				$parent = self::processField($obj, 'parent');
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
						echo 'invalid author passed in, commnet [' . $refId . '] skipped' . PHP_EOL;
						echo print_r($obj, true);
					}
					continue;
				}
				if(trim($parent) === '')
				{
					if($connector->debug === true)
					{
						echo 'invalid parent id passed in, commnet [' . $refId . '] skipped' . PHP_EOL;
						echo print_r($obj, true);
					}
					continue;
				}
				if(trim($content) === '')
				{
					if($connector->debug === true)
					{
						echo 'invalid content(message) passed in, commnet [' . $refId . '] skipped' . PHP_EOL;
						echo print_r($obj, true);
					}
					continue;
				}
				
				if($connector->debug === true)
				{
					$msg =  $rowCount . ': user data from forum' . PHP_EOL;
					$msg .= "\t refId(_id) => " . $refId . PHP_EOL;
					$msg .= "\t version(__v) => " . $version . PHP_EOL;
					$msg .= "\t parent => " . trim($parent) . PHP_EOL;
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
				
				$parentRefId = $parent;
				if(($parent = QuestionConnector::getById($parentRefId)) instanceof Question)
					$parent = $parent;
				elseif(($parent = AnswerConnector::getById($parentRefId)) instanceof Answer)
					$parent = $parent;
				else {
					if($connector->debug === true)
					{
						echo 'invalid parent passed in, commnet [' . $refId . '] skipped' . PHP_EOL;
						echo print_r($obj, true);
					}
					continue;
				}
				
				$authorRefId = $author;
				$author = PersonConnector::getById($authorRefId, $debug);
				if(!$author instanceof Person)
					continue;
				
				$transStarted = false;
				try {Dao::beginTransaction();} catch(Exception $e) {$transStarted = true;}
				
				$comments = $parent->addComments("", $content, $refId, $author, $authorName, $active);
				if($connector->debug === true)
					echo 'Comments[' . $comments->getId() . '] created/updated with title "' . $comments->getTitle() . '", content"' . $comments->getContent() . '" for ' . get_class($parent) . '[' . $parent->getId() . ']' . PHP_EOL;
				
				if(is_array($upVotes))
				{
					foreach ($upVotes as $upVote)
					{
						$person = PersonConnector::getById($upVote, $debug);
						if(!$person instanceof Person)
							continue;
						$comments->voteUp($person);
					}
				}
				foreach ($downVotes as $downVote)
				{
					$person = PersonConnector::getById($downVote);
					if(!$person instanceof Person)
						continue;
					$comments->voteDown($person);
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