<?php
class InfoAbstract extends BaseEntityAbstract
{
	/**
	 * The value of the information
	 * 
	 * @var string
	 */
	private $value;
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
	 * The class name of the entity
	 * @var unknown
	 */
	private $_entityClass;
	/**
	 * constructor
	 */
	public function __construct()
	{
		parent::__construct();
		$this->_entityClass = str_replace('Info', '', get_class($this));
	}
	/**
	 * Getter for the value
	 *
	 * @return string
	 */
	public function getValue()
	{
		return $this->value;
	}
	/**
	 * Setter for the value
	 *
	 * @param string $value The value for the information
	 *
	 * @return InfoAbstract
	 */
	public function setValue($value)
	{
		$this->value = $value;
		return $this;
	}
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
	 * Getter for the Type
	 * 
	 * @return InfoTypeAbstract
	 */
	public function getType() 
	{
		$this->loadManyToOne('type');
	    return $this->type;
	}
	/**
	 * Setter for the type
	 * 
	 * @param InfoTypeAbstract $value The type of the information
	 * 
	 * @return InfoAbstract
	 */
	public function setType($value) 
	{
	    $this->type = $value;
	    return $this;
	}
	/**
	 * Getter for the entity
	 * 
	 * @return InfoEntityAbstract
	 */
	public function getEntity() 
	{
		$method = 'get' . $this->_entityClass;
	    return $this->$method();
	}
	/**
	 * Setter for the enttiy
	 * 
	 * @param InfoEntityAbstract $value The entity 
	 * 
	 * @return InfoAbstract
	 */
	public function setEntity($value) 
	{
	    $method = 'set' . $this->_entityClass;
	    return $this->$method($value);
	}
	/**
	 * creating a new info object
	 * 
	 * @param InfoEntityAbstract $entity
	 * @param InfoTypeAbstract   $type
	 * @param string             $value
	 * 
	 * @return InfoAbstract
	 */
	public static function create(InfoEntityAbstract $entity, InfoTypeAbstract $type, $value = null, $entity = null, InfoAbstract &$exitsObj = null)
	{
		$className = get_called_class();
		if(!$entity instanceof BaseEntityAbstract && trim($value) === '')
			throw new Exception('must give entity or value');
		$value = trim($value) === '' ? null : trim($value);
		$entityName = $entity instanceof BaseEntityAbstract ? get_class($entity) : null;
		$entityId = $entity instanceof BaseEntityAbstract ? $entity->getId() : null;
		$info = ($exitsObj instanceof InfoAbstract ? $exitsObj : new $className());
		$info->setEntity($entity)
			->setType($type)
			->setValue($value)
			->setEntityName($entityName)
			->setEntityId($entityId)
			->save();
		return $info;
	}
	/**
	 * Getting all the information object
	 * 
	 * @param InfoEntityAbstract $entity
	 * @param InfoTypeAbstract   $type
	 * 
	 * @return Ambigous <multitype:, multitype:BaseEntityAbstract >
	 */
	public static function find(InfoEntityAbstract $entity, InfoTypeAbstract $type = null, $searchActiveOnly = true, $pageNo = null, $pageSize = DaoQuery::DEFAUTL_PAGE_SIZE, $orderBy = array(), &$stats = array())
	{
		$where = StringUtilsAbstract::lcFirst(str_replace('Info', '', get_called_class())) . 'Id = ?';
		$params = array($entity->getId());
		if($type instanceof InfoTypeAbstract)
		{
			$where .=' AND typeId = ?';
			$params[] = $type->getId();
		}
		return self::getAllByCriteria($where, $params, $searchActiveOnly, $pageNo, $pageSize, $orderBy, $stats);
	}
	/**
	 * removing all the information object
	 * 
	 * @param InfoEntityAbstract $entity
	 * @param InfoTypeAbstract   $type
	 * 
	 * @return Ambigous <multitype:, multitype:BaseEntityAbstract >
	 */
	public static function remove(InfoEntityAbstract $entity, InfoTypeAbstract $type = null)
	{
		$where = StringUtilsAbstract::lcFirst(str_replace('Info', '', get_called_class())) . 'Id = ?';
		$params = array($entity->getId());
		if($type instanceof InfoTypeAbstract)
		{
			$where .=' AND typeId = ?';
			$params[] = $type->getId();
		}
		self::updateByCriteria('active = 0', $where, $params);
	}
	/**
	 * (non-PHPdoc)
	 * @see HydraEntity::__loadDaoMap()
	 */
	public function __loadDaoMap()
	{
		DaoMap::setStringType('value', 'varchar', 255, true);
		DaoMap::setIntType('entityId', 'int', 10, true, true);
		DaoMap::setStringType('entityName','varchar', 50, true);
		DaoMap::setManyToOne(StringUtilsAbstract::lcFirst($this->_entityClass), $this->_entityClass, strtolower(get_class($this)) . '_entity');
		DaoMap::setManyToOne('type', get_class($this) . 'Type', strtolower(get_class($this)) . '_info_type');
		
		parent::__loadDaoMap();
		
		DaoMap::createIndex('value');
		DaoMap::createIndex('entityId');
		DaoMap::createIndex('entityName');
	}
}