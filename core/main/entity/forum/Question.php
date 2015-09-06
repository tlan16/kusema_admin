<?php
/** Question Entity
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
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
			
		}
		return parent::getJson($array, $reset);
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
	 * @param UserAccount	$author
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
	/**
	 * get Comments for this Question
	 * 
	 * @param string 	$criteria
	 * @param array 	$params
	 * @param bool	 	$activeOnly
	 * @param int	 	$pageNo
	 * @param int		$pageSize
	 * @param array		$orderBy
	 * @param array		$stats
	 * 
	 * @return array Comments
	 */
	public function getComments($criteria = '', $params = array(), $activeOnly = true, $pageNo = null, $pageSize = DaoQuery::DEFAUTL_PAGE_SIZE, $orderBy = array(), &$stats = array())
	{
		$criteria = trim($criteria) . (trim($criteria) === '' ? '' : ' and ') . 'entityName = ? and entityId = ?';
		$params[] = get_class();
		$params[] = $this->getId();
		$objs = Comments::getAllByCriteria($criteria, $params, $activeOnly, $pageNo, $pageSize, $orderBy, $stats);
		return $objs;
	}
	public static function getTopTopics($pageNo = 0, $pageSize = 10, $getObj = false, $getJson = false) {
		if(intval($pageNo) === 0 && intval($pageSize) === 0)
			throw new Exception('Invalid pageNo or pageSize passed in');
		$sql = "";
		$params = array();
		$sql.= "SELECT `q`.`entityId` `TopicId` , `t`.`name` `TopicName` "; 
		$sql.= ", COUNT(  `q`.`id` ) / ( ";
		$sql.= "	SELECT COUNT( `q`.`id` ) `total` ";
		$sql.= "	FROM  `questioninfo`  `q` ";
		$sql.= "	LEFT JOIN `topic` `t` ";
		$sql.= "	ON `t`.id = `q`.`entityId` ";
		$sql.= "	WHERE  `q`.`entityId` IS NOT NULL ";
		$sql.= "	AND `q`.`value` IS NULL ";
		$sql.= "	AND  `q`.`entityName` =  'Topic' ";
		$sql.= "	AND  `q`.`active` =1 ";
		$sql.= "	AND `t`.`active` =1 ";
		$sql.= ")*100 `percentage` ";
		$sql.= "FROM  `questioninfo`  `q` "; 
		$sql.= "LEFT JOIN `topic` `t` "; 
		$sql.= "ON `t`.id = `q`.`entityId` "; 
		$sql.= "WHERE  `q`.`entityId` IS NOT NULL ";
		$sql.= "AND  `q`.`entityName` =  'Topic' ";
		$sql.= "AND  `q`.`active` =1 ";
		$sql.= "AND  `t`.`active` =1 ";
		$sql.= "GROUP BY  `q`.`entityName` ,  `q`.`typeId` ,  `q`.`entityId` "; 
		$sql.= "ORDER BY  `percentage` DESC ";
		$sql.= "LIMIT " . intval($pageNo) . " , " . intval($pageSize) . " ";
		$queryResult = Dao::getResultsNative($sql, $params);
		$ids = array_map(create_function('$a', 'return $a["TopicId"];'), $queryResult);
		return ($getObj === true ? self::idToObject($ids, 'Topic', $getJson) : ($getJson === true ? json_encode($queryResult) : $queryResult));
	}
	public static function getTopUnits($pageNo = 0, $pageSize = 10, $getObj = false, $getJson = false) {
		if(intval($pageNo) === 0 && intval($pageSize) === 0)
			throw new Exception('Invalid pageNo or pageSize passed in');
		$getObj = (intval($getObj) === 1);
		$getJson = (intval($getJson) === 1);
		$sql = "";
		$params = array();
		$sql.= "SELECT `q`.`entityId` `UnitId` , `u`.`name` `UnitName` , `u`.`code` `UnitCode` "; 
		$sql.= ", COUNT(  `q`.`id` ) / ( ";
		$sql.= "	SELECT COUNT(  `q`.`id` )  `total` ";
		$sql.= "	FROM  `questioninfo`  `q` ";
		$sql.= "	LEFT JOIN `unit` `u` ";
		$sql.= "	ON `u`.id = `q`.`entityId` ";
		$sql.= "	WHERE  `q`.`entityId` IS NOT NULL ";
		$sql.= "	AND `q`.`value` IS NULL ";
		$sql.= "	AND  `q`.`entityName` =  'Unit' ";
		$sql.= "	AND  `q`.`active` =1 ";
		$sql.= "	AND `u`.`active` =1 ";
		$sql.= ")*100 `percentage` ";
		$sql.= "FROM  `questioninfo`  `q` "; 
		$sql.= "LEFT JOIN `unit` `u` "; 
		$sql.= "ON `u`.id = `q`.`entityId` "; 
		$sql.= "WHERE  `q`.`entityId` IS NOT NULL ";
		$sql.= "AND  `q`.`entityName` =  'Unit' ";
		$sql.= "AND  `q`.`active` =1 ";
		$sql.= "AND  `u`.`active` =1 ";
		$sql.= "GROUP BY  `q`.`entityName` ,  `q`.`typeId` ,  `q`.`entityId` "; 
		$sql.= "ORDER BY  `percentage` DESC ";
		$sql.= "LIMIT " . intval($pageNo) . " , " . intval($pageSize) . " ";
		$queryResult = Dao::getResultsNative($sql, $params);
		$ids = array_map(create_function('$a', 'return $a["UnitId"];'), $queryResult);
		return ($getObj === true ? self::idToObject($ids, 'Unit', $getJson) : ($getJson === true ? json_encode($queryResult) : $queryResult));
	}
	private static function idToObject($ids, $class, $json =false)
	{
		if(($class = trim($class)) === '' || !class_exists($class))
			throw new Exception('invalid class passed in ');
		if(!is_array($ids) && ($ids = intval($ids)) !== 0)
			$ids = array($ids);
		else $ids = array_unique($ids);
		$json = (intval($json) === 1);
		$result = array();
		foreach ($ids as $id)
		{
			if(($obj = $class::get($id)) instanceof $class)
			{
				$result[] = ($json === true ? $obj->getJson() : $obj);
			}
		}
		return $result;
	}
}