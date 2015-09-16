<?php
/**
 * UserProfile Entity
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class UserProfile extends BaseEntityAbstract
{
	/**
     * The person
     *
     * @var Person
     */
    protected $person;
    /**
     * The type of the UserProfile
     * @var UserProfileType
     */
    protected $type;
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
	 * getter Person
	 *
	 * @return Person
	 */
	public function getPerson()
	{
		$this->loadManyToOne("person");
		return $this->person;
	}
	/**
	 * Setter Person
	 *
	 * @param Person $Person The person that this UserProfileType belongs to
	 *
	 * @return UserProfileType
	 */
	public function setPerson(Person $Person)
	{
		$this->person = $Person;
		return $this;
	}
	/**
	 * getter for type
	 *
	 * @return UserProfileType
	 */
	public function getType()
	{
		$this->loadManyToOne('type');
	    return $this->type;
	}
	/**
	 * Setter for type
	 *
	 * @return UserProfile
	 */
	public function setType($type)
	{
	    $this->type = $type;
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
	 * @return UserProfile
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
	 * @return UserProfile
	 */
	public function setEntityName($value)
	{
		$this->entityName = $value;
		return $this;
	}
    
    /**
     * (non-PHPdoc)
     * @see BaseEntity::__loadDaoMap()
     */
    public function __loadDaoMap()
    {
        DaoMap::begin($this, 'up');
        DaoMap::setManyToOne("person", "Person", "p");
        DaoMap::setManyToOne('type', 'UserProfileType');
        DaoMap::setIntType('entityId');
		DaoMap::setStringType('entityName','varchar', 100);
        
        parent::__loadDaoMap();
        
        DaoMap::createIndex('entityId');
        DaoMap::createIndex('entityName');
        DaoMap::commit();
    }
    /**
     * create new UserProfile
     * 
     * @param Person 				$person
     * @param UserProfileType 		$type
     * @param BaseEntityAbstract 	$entity
     * @param bool	 				$active
     * 
     * @return UserProfile
     */
    public static function create(Person $person, UserProfileType $type, BaseEntityAbstract $entity, $active = true)
    {
    	$active = (intval($active) === 1);
    	$objs = self::getAllByCriteria('personId = ? and typeId = ? and entityId = ? and entityName = ?', array($person->getId(), $type->getId(), $entity->getId(), get_class($entity)), false, 1, 1);
    	$obj = count($objs) > 0 ? $objs[0] : new self();
    	$obj->setPerson($person)
    		->setType($type)
    		->setEntityId($entity->getId())
    		->setEntityName(get_class($entity))
    		->setActive($active)
    		->save();
    	return $obj;
    }
}
?>
