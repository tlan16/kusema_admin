<?php
/**
 * Topic Entity
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class Topic extends BaseEntityAbstract
{
    /**
     * The name of the Unit
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
     * @return Unit
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
        DaoMap::begin($this, 'tpc');
        DaoMap::setStringType('name', 'varchar', 100);
        
        parent::__loadDaoMap();
        
        DaoMap::createIndex('name');
        DaoMap::commit();
    }
    /**
     * create a new Topic
     * 
     * @param string $name
     * @param bool	 $active
     * 
     * @throws Exception
     * @return Topic
     */
    public static function create($name, $active = true)
    {
    	if(($name = trim($name)) === '')
    		throw new Exception('Name cannot be empty to create a new ' . __CLASS__);
    	$active = (intval($active) === 1);
    	$obj = self::getByName($name, false);
    	$obj = $obj instanceof self ? $obj : new self();
    	$obj->setName($name)
    		->setActive($active)
    		->save();
    	return $obj;
    }
    /**
     * get Topic by name
     * 
     * @param string $name
     * @param bool	 $activeOnly
     * 
     * @return Topic|null
     */
    public static function getByName($name, $activeOnly = true)
    {
    	$name = trim($name);
    	$activeOnly = (intval($activeOnly) === 1);
    	$objs = self::getAllByCriteria('name like ?', array($name), $activeOnly, 1, 1);
    	return count($objs) > 0 ? $objs[0] : null;
    }
    /**
     * get units for this Topic
     * 
     * @param bool $activeOnly
     * 
     * @return array Unit
     */
    public function getUnits($activeOnly = true)
    {
    	$activeOnly = (intval($activeOnly) === 1);
    	$objs = Unit_Topic::getByUnitAndTopic(null, $this, $activeOnly);
    	return $objs;
    }
    /**
     * add a Unit to Topic
     * 
     * @param Unit $unit
     * @param bool $active
     * 
     * @return Topic
     */
    public function addUnit(Unit $unit, $active = true) 
    {
    	$active = (intval($active) === 1);
    	$obj = Unit_Topic::create($unit, $this, $active);
    	return $this;
    }
}
?>
