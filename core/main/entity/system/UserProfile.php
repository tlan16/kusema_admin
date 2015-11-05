<?php
/**
 * UserProfile Entity
 *
 * @package    Core
 * @subpackage Entity
 * @author     flan<franklan118@gmail.com>
 */
class UserProfile extends BaseEntityAbstract
{
	/**
     * The UserAccount
     *
     * @var UserAccount
     */
    protected $userAccount;
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
	 * getter UserAccount
	 *
	 * @return UserAccount
	 */
	public function getUserAccount()
	{
		$this->loadManyToOne("userAccount");
		return $this->userAccount;
	}
	/**
	 * Setter UserAccount
	 *
	 * @param UserAccount $userAccount The UserAccount that this UserProfileType belongs to
	 *
	 * @return UserProfileType
	 */
	public function setUserAccount(UserAccount $userAccount)
	{
		$this->userAccount = $userAccount;
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
        DaoMap::setManyToOne("userAccount", "UserAccount", "u");
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
     * @param UserAccount 				$userAccount
     * @param UserProfileType 		$type
     * @param BaseEntityAbstract 	$entity
     * @param bool	 				$active
     * 
     * @return UserProfile
     */
    public static function create(UserAccount $userAccount, UserProfileType $type, BaseEntityAbstract $entity, $active = true)
    {
    	$active = (intval($active) === 1);
    	$objs = self::getAllByCriteria('userAccountId = ? and typeId = ? and entityId = ? and entityName = ?', array($userAccount->getId(), $type->getId(), $entity->getId(), get_class($entity)), false, 1, 1);
    	$obj = count($objs) > 0 ? $objs[0] : new self();
    	$obj->setUserAccount($userAccount)
    		->setType($type)
    		->setEntityId($entity->getId())
    		->setEntityName(get_class($entity))
    		->setActive($active)
    		->save();
    	return $obj;
    }
    /**
     * get roles by user account
     * 
     * @param UserAccount $userAccount
     * 
     * @return array Role
     */
    public static function getRolesByUserAccount(UserAccount $userAccount)
    {
    	$result = array();
    	$objs = self::getAllByCriteria('entityName = :eName and typeId = :typeId and userAccountId = :uid', array('eName' => 'Role', 'typeId' => UserProfileType::ID_ROLE, 'uid' => $userAccount->getId()));
    	if(!is_array($objs) || count($objs) === 0)
    		return $result;
    	foreach ($objs as $obj)
    	{
    		$role = Role::get(intval($obj->getEntityId()));
    		if(!$role instanceof Role)
    			continue;
    		$result[] = $role;
    	}
    	return $result;
    }
    /**
     * clear all roles for a user account
     * 
     * @param UserAccount $userAccount
     */
    public static function clearRolesByUserAccount(UserAccount $userAccount)
    {
    	self::deleteByCriteria('userAccountId = ? and typeId = ? and entityName = ?', array($userAccount->getId(), UserProfileType::ID_ROLE, 'Role'));
    }
    /**
     * add role to a user account
     * 
     * @param UserAccount $userAccount
     * @param Role $role
     * 
     * @return UserProfile
     */
    public static function addRoleByUserAccount(UserAccount $userAccount, Role $role)
    {
    	return self::create($userAccount, UserProfileType::get(UserProfileType::ID_ROLE), $role);
    }
    /**
     * remove a role for a user account
     * 
     * @param UserAccount $userAccount
     * @param Role $role
     */
    public static function removeRoleByUserAccount(UserAccount $userAccount, Role $role)
    {
    	self::deleteByCriteria('userAccountId = ? and typeId = ? and entityId = ? and entityName = ?', array($userAccount->getId(), UserProfileType::ID_ROLE, get_class($role), $role->getId()));
    }
}
?>
