<?php
class InfoEntityAbstract extends BaseEntityAbstract
{
	/**
	 * The title
	 *
	 * @var string
	 */
	private $title;
	/**
	 * The content
	 *
	 * @var string
	 */
	private $content;
	/**
	 * The author
	 *
	 * @var UserAccount
	 */
	protected $author = null;
	/**
	 * The author name
	 * 
	 * @var string
	 */
	private $authorName = null;
	/**
	 * The reference ID (e.g. kusema web forum id)
	 * 
	 * @var string
	 */
	private $refId = null;
	/**
	 * The cache for info
	 *
	 * @var array
	 */
	protected $_cache;
	/**
	 * The array of information
	 *
	 * @var multiple:InfoAbstract
	 */
	protected $infos;
	
	/**
	 * getter for title
	 *
	 * @return string
	 */
	public function getTitle()
	{
	    return $this->title;
	}
	/**
	 * Setter for title
	 *
	 * @return InfoEntityAbstract
	 */
	public function setTitle($title)
	{
	    $this->title = $title;
	    return $this;
	}
	/**
	 * getter for content
	 *
	 * @return string
	 */
	public function getContent()
	{
	    return $this->content;
	}
	/**
	 * Setter for content
	 *
	 * @return InfoEntityAbstract
	 */
	public function setContent($content)
	{
	    $this->content = $content;
	    return $this;
	}
	/**
	 * getter for author
	 *
	 * @return UserAccount|null
	 */
	public function getAuthor()
	{
		$this->loadManyToOne('author');
	    return $this->author;
	}
	/**
	 * getter for authorName
	 *
	 * @return string
	 */
	public function getAuthorName()
	{
	    return $this->authorName;
	}
	/**
	 * Setter for authorName
	 *
	 * @return InfoEntityAbstract
	 */
	public function setAuthorName($authorName)
	{
	    $this->authorName = $authorName;
	    return $this;
	}
	/**
	 * getter for refId
	 *
	 * @return string
	 */
	public function getRefId()
	{
	    return $this->refId;
	}
	/**
	 * Setter for refId
	 *
	 * @return InfoEntityAbstract
	 */
	public function setRefId($refId)
	{
	    $this->refId = $refId;
	    return $this;
	}
	/**
	 * Setter for author
	 *
	 * @return InfoEntityAbstract
	 */
	public function setAuthor($author)
	{
	    $this->author = $author;
	    return $this;
	}
	/**
	 * Getting all the information
	 *
	 * @return array
	 */
	public function getInfos()
	{
		$this->loadOneToMany('infos');
	    return $this->infos;
	}
	/**
	 * Setter for the information
	 *
	 * @param array $value The array of InfoAbstract
	 *
	 * @return InfoEntityAbstract
	 */
	public function setInfos($value)
	{
	    $this->infos = $value;
	    return $this;
	}
	/**
	 * Getting the
	 * @param int $typeId
	 * @param string $reset
	 * @throws EntityException
	 */
	public function getInfo($typeId, $reset = false)
	{
		DaoMap::loadMap($this);
		if(!isset($this->_cache[$typeId]) || $reset === true)
		{
			if(!isset(DaoMap::$map[strtolower(get_class($this))]['infos']) || ($class = trim(DaoMap::$map[strtolower(get_class($this))]['infos']['class'])) === '')
				throw new EntityException('You can NOT get information from a entity' . get_class($this) . ', setup the relationship first!');

			$sql = 'select value from ' . strtolower($class) . ' `info` where `info`.active = 1 and `info`.' . strtolower(get_class($this)) . 'Id = ? and `info`.TypeId = ?';
			$result = Dao::getResultsNative($sql, array($this->getId(), $typeId), PDO::FETCH_NUM);
			$this->_cache[$typeId] = array_map(create_function('$row', 'return $row[0];'), $result);
		}
		return $this->_cache[$typeId];
	}
	/**
	 * adding new value to this entity
	 *
	 * @param int  $typeId
	 * @param int  $value
	 * @param bool $overRideValue Whether we over write the value when we found one: clear all other value, and keep this new one
	 *
	 * @return InfoEntityAbstract
	 */
	public function addInfo($typeId, $value, $overRideValue = false)
	{
		DaoMap::loadMap($this);
		if(!isset(DaoMap::$map[strtolower(get_class($this))]['infos']) || ($class = trim(DaoMap::$map[strtolower(get_class($this))]['infos']['class'])) === '')
			throw new EntityException('You can NOT get information from a entity' . get_class($this) . ', setup the relationship first!');

		$InfoTypeClass = $class . 'Type';
		$infoType = $InfoTypeClass::get($typeId);
		if($overRideValue === true)
		{
			//clear all info
			$this->removeInfo($typeId);
			//create a new
			$info = $class::create($this, $infoType, $value);
		}
		else
		{
			//check whether we have one already
			$infos = $class::getAllByCriteria(strtolower(get_class($this)).'Id = ? and value = ? and typeId = ?', array($this->getId(), trim($typeId), trim($value)), true, 1 , 1);
			if(count($infos) > 0)
				return $this;
			//create new
			$info = $class::create($this, $infoType, $value);
		}

		//referesh cache
		$this->getInfo($typeId, true);
		return $this;
	}
	/**
	 * removing all information for that type
	 *
	 * @param int $typeId The type id
	 *
	 * @return InfoEntityAbstract
	 */
	public function removeInfo($typeId)
	{
		DaoMap::loadMap($this);
		if(!isset(DaoMap::$map[strtolower(get_class($this))]['infos']) || ($class = trim(DaoMap::$map[strtolower(get_class($this))]['infos']['class'])) === '')
			throw new EntityException('You can NOT get information from a entity' . get_class($this) . ', setup the relationship first!');

		$class::updateByCriteria('active = 0', 'typeId = ? and ' . strtolower(get_class($this)) . 'Id = ?', array($typeId, $this->getId()));
		unset($this->_cache[$typeId]);
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
			$array['createdBy'] = array('id'=> $this->getCreatedBy()->getId(), 'person' => $this->getCreatedBy()->getPerson()->getJson());
			$array['updatedBy'] = array('id'=> $this->getUpdatedBy()->getId(), 'person' => $this->getUpdatedBy()->getPerson()->getJson());
			$array['author'] = $this->getAuthor() instanceof UserAccount ? $this->getAuthor()->getJson() : null;
		}
		return parent::getJson($array, $reset);
	}
	/**
	 * (non-PHPdoc)
	 * @see BaseEntity::loadDaoMap()
	 */
	public function __loadDaoMap()
	{
		DaoMap::setOneToMany("infos", get_class($this) . "Info", strtolower(get_class($this)) . "_info");
		DaoMap::setStringType('title');
		DaoMap::setStringType('content','LONGTEXT');
		DaoMap::setStringType('authorName', 'varchar', 50, true, null);
		DaoMap::setManyToOne('author', 'UserAccount', get_class($this) . '_au', true);
		DaoMap::setStringType('refId', 'varchar', 25, true);
		
		parent::__loadDaoMap();
		
		DaoMap::createIndex('title');
		DaoMap::createIndex('authorName');
		DaoMap::createIndex('refId');
	}
	public static function getByRefId($refId, $activeOnly = true)
	{
		$class = get_called_class();
		$refId = trim($refId);
		$activeOnly = ($activeOnly === true);
		$objs = $class::getAllByCriteria('refId = ?', array($refId), $activeOnly, 1, 1);
		return count($objs) > 0 ? $objs[0] : null;
	}
}