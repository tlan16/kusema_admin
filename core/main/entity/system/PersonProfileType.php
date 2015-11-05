<?php
/**
 * PersonProfileType Entity
 *
 * @package    Core
 * @subpackage Entity
 * @author     flan<franklan118@gmail.com>
 */
class PersonProfileType extends BaseEntityAbstract
{
	const ID_SUBSCRIPTION = 1;
	const ID_ENROLLMENT = 2;
    /**
     * The name of the UserProfile
     * @var string
     */
    private $name;
    
    /**
     * getter for name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    /**
     * Setter for name
     *
     * @param string $name
     * @return UserProfileType
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
    
    /**
     * (non-PHPdoc)
     * @see BaseEntity::__loadDaoMap()
     */
    public function __loadDaoMap()
    {
        DaoMap::begin($this, 'pp_tp');
        DaoMap::setStringType('name', 'varchar', 100);
        
        parent::__loadDaoMap();
        
        DaoMap::createIndex('name');
        DaoMap::commit();
    }
    /**
     * create a new UserProfileType
     *
     * @param string $name
     * @param bool	 $active
     * 
     * @throws Exception
     * @return UserProfileType
     */
    public static function create($name, $active = true)
    {
    	if(($name = trim($name)) === '')
    		throw new Exception('Name cannot be empty to create a new ' . __CLASS__);
    	$active = (intval($active) === 1);
    	$obj = self::getByName($name);
    	$obj = $obj instanceof self ? $obj : new self();
    	$obj->setName($name)
	    	->setActive($active)
	    	->save();
    	return $obj;
    }
    /**
     * get UserProfileType by name
     *
     * @param string $name
     * @param bool	 $activeOnly
     * 
     * @return UserProfileType|null
     */
    public static function getByName($name, $activeOnly = true)
    {
    	$name = trim($name);
    	$activeOnly = (intval($activeOnly) === 1);
    	$objs = self::getAllByCriteria('name like ?', array($name), $activeOnly, 1, 1);
    	return count($objs) > 0 ? $objs[0] : null;
    }
}
?>
