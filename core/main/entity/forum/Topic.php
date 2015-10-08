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
     * The name of the Topic
     * @var string
     */
    private $name;
    /**
     * The reference id of a imported Topic
     * 
     * @var string
     */
    private $refId;
    
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
     * @return Topic
     */
    public function setName($name)
    {
        $this->name = $name;
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
     * @return Topic
     */
    public function setRefId($refId)
    {
        $this->refId = $refId;
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
        DaoMap::setStringType('refId', 'varchar', 50);
        
        parent::__loadDaoMap();
        
        DaoMap::createIndex('name');
        DaoMap::createIndex('refId');
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
    public static function create($name, $refId = '', $active = true)
    {
    	if(($name = trim($name)) === '')
    		throw new Exception('Name cannot be empty to create a new ' . __CLASS__);
    	$refId = trim($refId);
    	$active = (intval($active) === 1);
    	
    	if($refId !== '' && ($obj = self::getByRefId($refId)) instanceof self)
    	{
    		$obj = $obj;
    		$refId = $obj->getRefId();
    	}
    	elseif(($obj = self::getByName($name)) instanceof self)
    		$obj = $obj;
    	else $obj = new self();
    	
    	$obj->setName($name)
    		->setRefId($refId)
    		->setActive($active)
    		->save();
    	return $obj;
    }
    public static function getByRefId($refId, $activeOnly = true)
    {
    	$refId = trim($refId);
    	$activeOnly = (intval($activeOnly) === 1);
    	$objs = self::getAllByCriteria('refId = ?', array($refId), $activeOnly, 1, 1);
    	return ( count($objs) > 0 ? $objs[0] : null );
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
