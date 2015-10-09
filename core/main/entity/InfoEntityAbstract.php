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
	 * @var Person
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
	protected function getInfo($typeId, $entityName = null, $entityId = null, $value = null, $reset = false)
	{
		DaoMap::loadMap($this);
		$cacheKey = trim($typeId) . trim($entityName) . trim($entityId);
		if(!isset($this->_cache[$cacheKey]) || $reset === true)
		{
			if(!isset(DaoMap::$map[strtolower(get_class($this))]['infos']) || ($class = trim(DaoMap::$map[strtolower(get_class($this))]['infos']['class'])) === '')
				throw new EntityException('You can NOT get information from a entity' . get_class($this) . ', setup the relationship first!');

			$sql = 'select id from ' . strtolower($class) . ' `info` where `info`.active = 1 and `info`.' . strtolower(get_class($this)) . 'Id = ? and `info`.typeId = ?';
			$params =  array($this->getId(), $typeId);
			if(/**$entityName === null || **/trim($entityName) !== '')
			{
				$sql .= $entityName === null ? ' and `info`.entityName is NULL' : ' and `info`.entityName = ?';
				if($entityName !== null)
					$params[] =  trim($entityName);
			}
			if(/**$entityId === null || **/intval($entityId) !== 0)
			{
				$sql .= $entityId === null ? ' and `info`.entityId is NULL' : ' and `info`.entityId = ?';
				if($entityId !== null)
					$params[] = intval($entityId);
			}
			if(/**$value === null || **/trim($value) !== '')
			{
				$sql .= $value === null ? ' and `info`.value is NULL' : ' and `info`.value = ?';
				if($value !== null)
					$params[] = trim($value);
			}
			$result = Dao::getResultsNative($sql, $params, PDO::FETCH_NUM);
			$this->_cache[$cacheKey] = array_map(create_function('$row', 'return ' . $class . '::get($row[0]);'), $result);
		}
		return $this->_cache[$cacheKey];
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
	protected function addInfo($typeId, $entity = null, $value = null, $overRideValue = false)
	{
		DaoMap::loadMap($this);
		if(!isset(DaoMap::$map[strtolower(get_class($this))]['infos']) || ($class = trim(DaoMap::$map[strtolower(get_class($this))]['infos']['class'])) === '')
			throw new EntityException('You can NOT get information from a entity' . get_class($this) . ', setup the relationship first!');

		$InfoTypeClass = $class . 'Type';
		$infoType = $InfoTypeClass::get($typeId);
		$typeId = ($typeId === null ? null : intval($typeId));
		$value = StringUtilsAbstract::nullOrString($value);
		$entityId = $entity instanceof BaseEntityAbstract ? $entity->getId() : null;
		$entityName = $entity instanceof BaseEntityAbstract ? get_class($entity) : null;
		if($overRideValue === true)
		{
			//clear all info
			$this->removeInfo($typeId);
			//create a new
			$info = $class::create($this, $infoType, $value, $entity);
		}
		else
		{
			//check whether we have one already
			$infos = $this->getInfo($typeId, $entityName, $entityId, $value);
			$info = count($infos) > 0 ? $infos[0] : $class::create($this, $infoType, $value, $entity);		
			$info->setActive(true)->save();
		}

		//referesh cache
		$this->getInfo($typeId, $entityName, $entityId, true);
		return $this;
	}
	/**
	 * removing all information for that type
	 *
	 * @param int $typeId The type id
	 *
	 * @return InfoEntityAbstract
	 */
	protected function removeInfo($typeId)
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
			$array['createdBy'] = array('id'=> $this->getCreatedBy()->getId(), 'person' => $this->_getPersonJson($this->getCreatedBy()->getPerson()) );
			$array['updatedBy'] = array('id'=> $this->getUpdatedBy()->getId(), 'person' => $this->_getPersonJson($this->getUpdatedBy()->getPerson()) );
			$array['author'] = $this->getAuthor() instanceof Person ? $this->_getPersonJson($this->getAuthor()) : null;
			if(($topics = $this->getTopics()) && count($topics) > 0)
				$array['topics'] = array_map(create_function('$a', 'return $a->getJson();'), $topics);
			else $array['topics'] = array();
			if(($units = $this->getUnits()) && count($units) > 0)
				$array['units'] = array_map(create_function('$a', 'return $a->getJson();'), $units);
			else $array['units'] = array();
// 			if(($votes = $this->getVotes()) && count($votes) > 0)
// 				$array['votes'] = array_map(create_function('$a', '$b=$a->getJson();$id=$a->getId();return $b["Vote"] ? array($id=>$b["Vote"]) : null;'), $votes);
		}
		return parent::getJson($array, $reset);
	}
	private function _getPersonJson(Person $person)
	{
		return array('id'=> $person->getId(), 
					'firstName'=> $person->getFirstName(),
					'lastName'=> $person->getLastName(),
					'fullName'=> $person->getFullName(),
					'email'=> $person->getEmail()
					);
	}
	/**
	 * (non-PHPdoc)
	 * @see BaseEntity::loadDaoMap()
	 */
	public function __loadDaoMap()
	{
		DaoMap::setOneToMany("infos", get_class($this) . "Info", strtolower(get_class($this)) . "_info");
		DaoMap::setStringType('title', 'varchar', 100);
		DaoMap::setStringType('content','LONGTEXT');
		DaoMap::setStringType('authorName', 'varchar', 50, true, null);
		DaoMap::setManyToOne('author', 'Person', get_class($this) . '_au', true);
		DaoMap::setStringType('refId', 'varchar', 50, true);
		
		parent::__loadDaoMap();
		
		DaoMap::createIndex('title');
		DaoMap::createIndex('authorName');
		DaoMap::createIndex('refId');
	}
	public static function getByRefId($refId, $activeOnly = true)
	{
		$class = get_called_class();
		$refId = trim($refId);
		$activeOnly = (intval($activeOnly) === 1);
		$objs = $class::getAllByCriteria('refId = ?', array($refId), intval($activeOnly), 1, 1);
		return count($objs) > 0 ? $objs[0] : null;
	}
	public function voteUp(Person $person, $overRideValue = false)
	{
		DaoMap::loadMap($this);
		if(!isset(DaoMap::$map[strtolower(get_class($this))]['infos']) || ($class = trim(DaoMap::$map[strtolower(get_class($this))]['infos']['class'])) === '')
			throw new EntityException('You can NOT get information from a entity' . get_class($this) . ', setup the relationship first!');
		$InfoTypeClass = $class . 'Type';
		return $this->vote($class::VALUE_VOTE_UP, $person, $overRideValue);
	}
	public function voteDown(Person $person, $overRideValue = false)
	{
		DaoMap::loadMap($this);
		if(!isset(DaoMap::$map[strtolower(get_class($this))]['infos']) || ($class = trim(DaoMap::$map[strtolower(get_class($this))]['infos']['class'])) === '')
			throw new EntityException('You can NOT get information from a entity' . get_class($this) . ', setup the relationship first!');
		$InfoTypeClass = $class . 'Type';
		return $this->vote($class::VALUE_VOTE_DOWN, $person, $overRideValue);
	}
	private function vote($voteType, Person $person, $overRideValue = false)
	{
		DaoMap::loadMap($this);
		if(!isset(DaoMap::$map[strtolower(get_class($this))]['infos']) || ($class = trim(DaoMap::$map[strtolower(get_class($this))]['infos']['class'])) === '')
			throw new EntityException('You can NOT get information from a entity' . get_class($this) . ', setup the relationship first!');
		$InfoTypeClass = $class . 'Type';
		
		$typeId = $InfoTypeClass::ID_VOTE;
		if(($typeId = intval($typeId)) === 0)
			throw new Exception('cannot find const ID_VOTE under class ' . $InfoTypeClass);
		return $this->addInfo($typeId, $person, intval($voteType), $overRideValue);
	}
	public function getVotes($entityName = 'Person', $entityId = null, $value = null, $reset = false)
	{
		$value = (intval($value) === 0 ? null : intval($value));
		
		DaoMap::loadMap($this);
		if(!isset(DaoMap::$map[strtolower(get_class($this))]['infos']) || ($class = trim(DaoMap::$map[strtolower(get_class($this))]['infos']['class'])) === '')
			throw new EntityException('You can NOT get information from a entity' . get_class($this) . ', setup the relationship first!');
		$InfoTypeClass = $class . 'Type';
		
		$typeId = $InfoTypeClass::ID_VOTE;
		if(trim($typeId) === '')
			return null;
		
		return $this->getInfo($typeId, $entityName, $entityId, $value, $reset);
	}
	public function getVoteNumber($entityName = 'Person', $entityId = null, $value = null, $reset = false)
	{
		$votes = $this->getVotes($entityName, $entityId, $value, $reset);
		$result = 0;
		foreach ($votes as $vote)
		{
			if(intval($vote->getValue()) === intval(InfoAbstract::VALUE_VOTE_UP))
				$result += 1;
			elseif(intval($vote->getValue()) === intval(InfoAbstract::VALUE_VOTE_DOWN))
				$result -= 1;
		}
		return $result;
	}
	public function addTopic(Topic $topic, $overRideValue = false)
	{
		DaoMap::loadMap($this);
		if(!isset(DaoMap::$map[strtolower(get_class($this))]['infos']) || ($class = trim(DaoMap::$map[strtolower(get_class($this))]['infos']['class'])) === '')
			throw new EntityException('You can NOT get information from a entity' . get_class($this) . ', setup the relationship first!');
		$InfoTypeClass = $class . 'Type';
		
		$typeId = $InfoTypeClass::ID_TOPIC;
		return $this->addInfo($typeId, $topic, null, $overRideValue);
	}
	public function getTopics($entityName = 'Topic', $entityId = null, $value = null, $reset = false)
	{
		$value = intval($value) === 0 ? null : intval($value);
	
		DaoMap::loadMap($this);
		if(!isset(DaoMap::$map[strtolower(get_class($this))]['infos']) || ($class = trim(DaoMap::$map[strtolower(get_class($this))]['infos']['class'])) === '')
			throw new EntityException('You can NOT get information from a entity' . get_class($this) . ', setup the relationship first!');
		$InfoTypeClass = $class . 'Type';
	
		$typeId = $InfoTypeClass::ID_TOPIC;
		$entityId = ($entityId === null ? '' : intval($entityId));
		if(trim($typeId) === '')
			throw new Exception($InfoTypeClass . '::ID_TOPIC is empty or invalid');
		
		$infos = $this->getInfo($typeId, $entityName, $entityId, $value, $reset);
		
		$result = array();
		if(is_array($infos))
		{
			foreach ($infos as $info)
			{
				if($info->getEntityId() && ($topic = Topic::get($info->getEntityId())) instanceof Topic)
					$result[] = $topic;
			}
		}
		return $result;
	}
	public function addUnit(Unit $unit, $overRideValue = false)
	{
		DaoMap::loadMap($this);
		if(!isset(DaoMap::$map[strtolower(get_class($this))]['infos']) || ($class = trim(DaoMap::$map[strtolower(get_class($this))]['infos']['class'])) === '')
			throw new EntityException('You can NOT get information from a entity' . get_class($this) . ', setup the relationship first!');
		$InfoTypeClass = $class . 'Type';
		
		$typeId = $InfoTypeClass::ID_UNIT;
		return $this->addInfo($typeId, $unit, null, $overRideValue);
	}
	public function getUnits($entityName = 'Unit', $entityId = null, $value = null, $reset = false)
	{
		$value = (intval($value) === 0 ? null : intval($value));
	
		DaoMap::loadMap($this);
		if(!isset(DaoMap::$map[strtolower(get_class($this))]['infos']) || ($class = trim(DaoMap::$map[strtolower(get_class($this))]['infos']['class'])) === '')
			throw new EntityException('You can NOT get information from a entity' . get_class($this) . ', setup the relationship first!');
		$InfoTypeClass = $class . 'Type';
	
		$typeId = $InfoTypeClass::ID_UNIT;
		$entityId = ($entityId === null ? '' : intval($entityId));
		if(trim($typeId) === '')
			throw new Exception($InfoTypeClass . '::ID_UNIT is empty or invalid');
		
		$infos =  $this->getInfo($typeId, $entityName, $entityId, $value, $reset);
		$result = array();
		if(is_array($infos))
		{
			foreach ($infos as $info)
			{
				if($info->getEntityId() && ($unit = Unit::get($info->getEntityId())) instanceof Unit)
					$result[] = $unit;
			}
		}
		return $result;
	}
}