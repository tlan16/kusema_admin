<?php
/** Comments Entity
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class Comments extends InfoEntityAbstract
{
	/**
	 * The id of the entity
	 *
	 * @var int
	 */
	private $entityId;
	/**
	 * The name of the entity
	 *
	 * @var string
	 */
	private $entityName;
	/**
	 * Getter for EntityId
	 *
	 * @return int
	 */
	public function getEntityId()
	{
		return $this->entityId;
	}
	/**
	 * Setter for entity
	 *
	 * @param int $value The entity id
	 *
	 * @return Comments
	 */
	public function setEntityId($value)
	{
		$this->entityId = $value;
		return $this;
	}
	/**
	 * Getter for entityName
	 *
	 * @return string
	 */
	public function getEntityName()
	{
		return $this->entityName;
	}
	/**
	 * Setter for entityName
	 *
	 * @param string $value The entityName
	 *
	 * @return Comments
	 */
	public function setEntityName($value)
	{
		$this->entityName = $value;
		return $this;
	}
	/**
	 * (non-PHPdoc)
	 * @see BaseEntityAbstract::getJson()
	 */
	public function getJson($extra = array(), $reset = false)
	{
		$array = $extra;
	    if(!$this->isJsonLoaded($reset))
	    {
	    	if($this->getEntityName() === 'Question')
	    		$array['question'] = ($obj = Question::get($this->getEntityId())) instanceof Question ? $obj->getJson() : null;
	    	if($this->getEntityName() === 'Answer')
	    		$array['question'] = ($obj = Answer::get($this->getEntityId())) instanceof Answer ? $obj->getJson() : null;
	    	if($this->getEntityName() === 'Comments')
	    		$array['question'] = ($obj = Comments::get($this->getEntityId())) instanceof Comments ? $obj->getJson() : null;
	    }
	    return parent::getJson($array, $reset);
	}
	/**
	 * (non-PHPdoc)
	 * @see BaseEntity::__loadDaoMap()
	 */
	public function __loadDaoMap()
	{
		DaoMap::begin($this, 'comm');
				
		parent::__loadDaoMap();
		
		DaoMap::setIntType('entityId');
		DaoMap::setStringType('entityName','varchar', 100);
		
		DaoMap::createIndex('entityId');
		DaoMap::createIndex('entityName');

		DaoMap::commit();
	}
	/**
	 * create a Comments
	 * 
	 * @param string 				$title
	 * @param string 				$content
	 * @param BaseEntityAbstract 	$entity
	 * @param string	 			$refId
	 * @param UserAccount			$author
	 * @param string 				$authorName
	 * @param bool	 				$active
	 * 
	 * @throws Exception
	 * @return Comments
	 */
	public static function create($title, $content, BaseEntityAbstract $entity, $refId = null, $author = null, $authorName = null, $active = true)
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
			->setEntityName(get_class($entity))
			->setEntityId($entity->getId())
			->setRefId($refId)
			->setAuthor($author)
			->setAuthorName($authorName)
			->setActive($active)
			->save();
		return $obj;
	}
	/**
	 * create a Comments by Question
	 * 
	 * @param string 	$title
	 * @param string 	$content
	 * @param Question 	$question
	 * @param string 	$refId
	 * @param string 	$author
	 * @param string 	$authorName
	 * @param string 	$active
	 */
	public static function createByQuestion($title, $content, Question $question, $refId = null, $author = null, $authorName = null, $active = true) 
	{
		$obj = self::create($title, $content, $question, $refId, $author, $authorName, $active);
		return $obj;
	}
	/**
	 * create a Comments by Answer
	 * 
	 * @param string 	$title
	 * @param string 	$content
	 * @param Answer 	$answer
	 * @param string 	$refId
	 * @param string 	$author
	 * @param string 	$authorName
	 * @param string 	$active
	 */
	public static function createByAnswer($title, $content, Answer $answer, $refId = null, $author = null, $authorName = null, $active = true) 
	{
		$obj = self::create($title, $content, $answer, $refId, $author, $authorName, $active);
		return $obj;
	}
	/**
	 * create a comment for this Comments
	 *
	 * @param string 		$title
	 * @param string 		$content
	 * @param string 		$refId
	 * @param UserAccount 	$author
	 * @param string 		$authorName
	 * @param bool	 		$active
	 */
	public function addComments($title, $content, $refId = null, $author = null, $authorName = null, $active = true) {
		$obj = Comments::createByQuestion($title, $content, $this, $refId, $author, $authorName, $active);
		return $obj;
	}
	/**
	 * get Comments for this Comments
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