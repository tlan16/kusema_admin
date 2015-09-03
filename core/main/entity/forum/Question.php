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
}