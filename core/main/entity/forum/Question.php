<?php
/** Question Entity
 *
 * @package    Core
 * @subpackage Entity
 * @author     flan<franklan118@gmail.com>
 */
class Question extends InfoEntityAbstract
{
	/**
	 * (non-PHPdoc)
	 * @see BaseEntityAbstract::getJson()
	 */
	public function getJson($extra = array(), $reset = false)
	{
		$array = $extra;
		if(!$this->isJsonLoaded($reset))
		{
			$array['vote'] = $this->getVoteNumber();
		}
		return parent::getJson($array, $reset);
	}
	/**
	 * get all answers for this question
	 * 
	 * @return array Answer
	 */
	public function getAnswers()
	{
		return Answer::getByQuestion($this);
	}
	/**
	 * get all comments for this question
	 * 
	 * @return array Comments
	 */
	public function getComments()
	{
		return Comments::getByQuestion($this);
	}
	/**
	 * (non-PHPdoc)
	 * @see BaseEntity::__loadDaoMap()
	 */
	public function __loadDaoMap()
	{
		DaoMap::begin($this, 'quest');
				
		parent::__loadDaoMap();

		DaoMap::commit();
	}
	/**
	 * create a Question
	 * 
	 * @param string 			$title
	 * @param string 			$content
	 * @param string			$refId
	 * @param UserAccount	 	$author
	 * @param string			$authorName
	 * @param bool	 			$active
	 * 
	 * @return Question
	 * @throws Exception
	 */
	public static function create($title, $content, $refId = null, $author = null, $authorName = null, $active = true)
	{
		if(($title = trim($title)) === '')
			throw new Exception('Title for a ' . __CLASS__ . ' must not be empty');
		if(($content = trim($content)) === '')
			throw new Exception('Content for a ' . __CLASS__ . ' must be null or a non-empty string');
		if(($refId = StringUtilsAbstract::nullOrString($refId)) !== null && $refId === '')
			throw new Exception('RefId for a ' . __CLASS__ . ' must not be empty');
		if($author !== null && !$author instanceof Person)
			throw new Exception('Author for a ' . __CLASS__ . ' must be null or instance of Person');
		if(($authorName = StringUtilsAbstract::nullOrString($authorName)) !== null && $authorName === '')
			throw new Exception('AuthorName for a ' . __CLASS__ . ' must not be empty');
		
		$active = (intval($active) === 1);
		
		$obj = self::getByRefId($refId, false); // deactived obj will also be found and replaced
		$obj = $obj instanceof self ? $obj : new self();
		$obj->setTitle($title)
			->setContent($content)
			->setRefId($refId)
			->setAuthor($author)
			->setAuthorName($authorName)
			->setActive($active)
			->save();
		return $obj;
	}
	/**
	 * create an Answer for this Question
	 * 
	 * @param string 		$title
	 * @param string 		$content
	 * @param string 		$refId
	 * @param Person		$author
	 * @param string 		$authorName
	 * @param bool			$active
	 * 
	 * @return Answer
	 */
	public function addAnswer($title, $content, $refId = null, $author = null, $authorName = null, $active = true)
	{
		$obj = Answer::createByQuestion($title, $content, $this, $refId, $author, $authorName, $active);
		return $obj;
	}
	/**
	 * create a comment for this Question
	 * 
	 * @param string 		$title
	 * @param string 		$content
	 * @param string 		$refId
	 * @param UserAccount 	$author
	 * @param string 		$authorName
	 * @param bool	 		$active
	 * 
	 * @return Comments
	 */
	public function addComments($title, $content, $refId = null, $author = null, $authorName = null, $active = true) {
		$obj = Comments::createByQuestion($title, $content, $this, $refId, $author, $authorName, $active);
		return $obj;
	}
	public static function getTopTopics($pageNo = 0, $pageSize = 10, $getObj = false, $getJson = false) 
	{
		if(intval($pageNo) === 0 && intval($pageSize) === 0)
			throw new Exception('Invalid pageNo or pageSize passed in');
		$getObj = (intval($getObj) === 1);
		$getJson = (intval($getJson) === 1);
		$sql = "";
		$params = array();
		$sql.= "SELECT `q`.`entityId` `TopicId` , `t`.`name` `TopicName` , COUNT(  `q`.`id` ) `count`"; 
		$sql.= "FROM  `questioninfo`  `q` "; 
		$sql.= "LEFT JOIN `topic` `t` "; 
		$sql.= "ON `t`.id = `q`.`entityId` "; 
		$sql.= "WHERE  `q`.`entityId` IS NOT NULL ";
		$sql.= "AND  `q`.`entityName` =  'Topic' ";
		$sql.= "AND  `q`.`active` =1 ";
		$sql.= "AND  `t`.`active` =1 ";
		$sql.= "GROUP BY  `q`.`entityName` ,  `q`.`typeId` ,  `q`.`entityId` "; 
		$sql.= "ORDER BY  `count` DESC ";
		$sql.= "LIMIT " . intval($pageNo) . " , " . intval($pageSize) . " ";
		$queryResult = Dao::getResultsNative($sql, $params);
		$sql = "";
		$sql.= "SELECT COUNT(  `q`.`id` )  `total` ";
		$sql.= "FROM  `questioninfo`  `q` ";
		$sql.= "LEFT JOIN `topic` `t` ";
		$sql.= "ON `t`.id = `q`.`entityId` ";
		$sql.= "WHERE  `q`.`entityId` IS NOT NULL ";
		$sql.= "AND `q`.`value` IS NULL ";
		$sql.= "AND  `q`.`entityName` =  'Topic' ";
		$sql.= "AND  `q`.`active` =1 ";
		$sql.= "AND `t`.`active` =1 ";
		$total = Dao::getResultsNative($sql);
		$total = intval($total[0]['total']);
		$ids = array_map(create_function('$a', 'return $a["TopicId"];'), $queryResult);
		foreach ($queryResult as &$item)
		{ $item['percentage'] = (100 * intval($item['count']) / $total); }
		if($getObj === true)
		{
			$result = array();
			foreach ($queryResult as $item)
			{ $result[] = self::idToObject($item['TopicId'], 'Topic', $getJson, array('percentage' => (100 * intval($item['count']) / $total) )); }
			return $result;
		} else return $queryResult;
	}
	public static function getTopUnits($pageNo = 0, $pageSize = 10, $getObj = false, $getJson = false) 
	{
		if(intval($pageNo) === 0 && intval($pageSize) === 0)
			throw new Exception('Invalid pageNo or pageSize passed in');
		$getObj = (intval($getObj) === 1);
		$getJson = (intval($getJson) === 1);
		$sql = "";
		$params = array();
		$sql.= "SELECT `q`.`entityId` `UnitId` , `u`.`name` `UnitName` , `u`.`code` `UnitCode` , COUNT(  `q`.`id` ) `count`"; 
		$sql.= "FROM  `questioninfo`  `q` "; 
		$sql.= "LEFT JOIN `unit` `u` "; 
		$sql.= "ON `u`.id = `q`.`entityId` "; 
		$sql.= "WHERE  `q`.`entityId` IS NOT NULL ";
		$sql.= "AND  `q`.`entityName` =  'Unit' ";
		$sql.= "AND  `q`.`active` =1 ";
		$sql.= "AND  `u`.`active` =1 ";
		$sql.= "GROUP BY  `q`.`entityName` ,  `q`.`typeId` ,  `q`.`entityId` "; 
		$sql.= "ORDER BY  `count` DESC ";
		$sql.= "LIMIT " . intval($pageNo) . " , " . intval($pageSize) . " ";
		$queryResult = Dao::getResultsNative($sql, $params);
		$sql = "";
		$sql.= "SELECT COUNT(  `q`.`id` )  `total` ";
		$sql.= "FROM  `questioninfo`  `q` ";
		$sql.= "LEFT JOIN `unit` `u` ";
		$sql.= "ON `u`.id = `q`.`entityId` ";
		$sql.= "WHERE  `q`.`entityId` IS NOT NULL ";
		$sql.= "AND `q`.`value` IS NULL ";
		$sql.= "AND  `q`.`entityName` =  'Unit' ";
		$sql.= "AND  `q`.`active` =1 ";
		$sql.= "AND `u`.`active` =1 ";
		$total = Dao::getResultsNative($sql);
		$total = intval($total[0]['total']);
		$ids = array_map(create_function('$a', 'return $a["UnitId"];'), $queryResult);
		foreach ($queryResult as &$item)
		{ $item['percentage'] = (100 * intval($item['count']) / $total); }
		if($getObj === true)
		{
			$result = array();
			foreach ($queryResult as $item)
			{ $result[] = self::idToObject($item['UnitId'], 'Unit', $getJson, array('percentage' => (100 * intval($item['count']) / $total) )); }
			return $result;
		}
		else return $queryResult;
	}
	private static function idToObject($id, $class, $json = false, $extra = array())
	{
		if(($class = trim($class)) === '' || !class_exists($class = ucfirst($class)))
			throw new Exception('invalid class passed in ');
		if(!is_array($extra))
			throw new Exception('invalid extra for getJson() passed in');
		if(is_array($id) || ($id = intval($id)) === 0)
			throw new Exception('Invalid id passed in');
		$json = (intval($json) === 1);
		return ( ($obj = $class::get($id)) instanceof $class ? ($json === true ? $obj->getJson($extra) : $obj) : null );
	}
	public static function getQuestions(array $title = array(), $content = '', $authorId = 0, $authorName = '', $refId = '', $vote = '', $active = true, array $created = array(), array $updated = array(), array $topics = array(), array $units = array(), $pageNo = null, $pageSize = DaoQuery::DEFAUTL_PAGE_SIZE, $orderBy = array(), &$stats = array())
	{
		$where = array(1);
		$params = array();
		$innerJoins = array();
		
		if(isset($title['txt']) && ($searchTxt = trim($title['txt'])) !== '')
		{
			$field = 'title';
			if(isset($title['token']) && $title['token'] === true)
			{
				$searchTokens = array();
				StringUtilsAbstract::permute(preg_split("/[\s,]+/", $searchTxt), $searchTokens);
				$likeArray = array();
				foreach($searchTokens as $index => $tokenArray) {
					$key = $field . $index;
					$params[$key] = '%' . implode('%', $tokenArray) . '%';
					$likeArray[] = $field . " like :" . $key;
				}
				$where[] = '(' . implode(' OR ', $likeArray) . ')';
			} else {
				$where[] = 'quest.title like :title';
				$params[$field] = '%' . $searchTxt . '%';
			}
		}
		
		if(isset($content['txt']) && ($searchTxt = trim($content['txt'])) !== '')
		{
			$field = 'content';
			if(isset($content['token']) && $content['token'] === true)
			{
				$searchTokens = array();
				StringUtilsAbstract::permute(preg_split("/[\s,]+/", $searchTxt), $searchTokens);
				$likeArray = array();
				foreach($searchTokens as $index => $tokenArray) {
					$key = $field . $index;
					$params[$key] = '%' . implode('%', $tokenArray) . '%';
					$likeArray[] = $field . " like :" . $key;
				}
				$where[] = '(' . implode(' OR ', $likeArray) . ')';
			} else {
				$where[] = 'quest.content like :content';
				$params[$field] = '%' . $searchTxt . '%';
			}
		}
		
		if($active === true || $active === false)
		{
			$where[] = 'quest.active = :active';
			$params['active'] = intval($active);
		}
		
		if(count($topics) > 0)
		{
			$field = 'tpcId';
			$ps = array();
			$keys = array();
			foreach($topics as $index => $value){
				$key = $field . '_' . $index;
				$keys[] = ':' . $key;
				$ps[$key] = trim($value);
			}
			$key = $field . '_' . 'entityName';
			$ps[$key] = 'Topic';
			self::getQuery()->eagerLoad('Question.infos', 'inner join', 'quest_info_tpc', 'quest.id = quest_info_tpc.questionId and quest_info_tpc.entityName = :' . $key . ' and quest_info_tpc.entityId in (' . implode(',', $keys) . ')');
			$params = array_merge($params, $ps);
		}
		
		if(count($units) > 0)
		{
			$field = 'unitId';
			$ps = array();
			$keys = array();
			foreach($units as $index => $value){
				$key = $field . '_' . $index;
				$keys[] = ':' . $key;
				$ps[$key] = trim($value);
			}
			$key = $field . '_' . 'entityName';
			$ps[$key] = 'Unit';
			self::getQuery()->eagerLoad('Question.infos', 'inner join', 'quest_info_unit', 'quest.id = quest_info_unit.questionId and quest_info_unit.entityName = :' . $key . ' and quest_info_unit.entityId in (' . implode(',', $keys) . ')');
			$params = array_merge($params, $ps);
		}
		
		if(isset($created['from']) && $created['from'] instanceof UDate)
		{
			$where[] = 'quest.created >= :created_from';
			$params['created_from'] = trim($created['from']);
		}
		
		if(isset($created['to']) && $created['to'] instanceof UDate)
		{
			$where[] = 'quest.created <= :created_to';
			$params['created_to'] = trim($created['to']);
		}
		
		return Question::getAllByCriteria(implode(' AND ', $where), $params, false, $pageNo, $pageSize, $orderBy, $stats);
	}
}