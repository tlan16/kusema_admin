<?php
/**
 * Unit Entity
 *
 * @package    Core
 * @subpackage Entity
 * @author     flan<franklan118@gmail.com>
 */
class Unit extends BaseEntityAbstract
{
    /**
     * The name of the Unit
     * @var string
     */
    private $name;
    /**
     * The reference id of a imported Unit
     *
     * @var string
     */
    private $refId;
    /**
     * The code of the Unit
     * 
     * @var string
     */
    private $code;
    /**
     * The year level of the Unit
     * 
     * @var int
     */
    private $year = null;
    
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
     * @return Unit
     */
    public function setRefId($refId)
    {
        $this->refId = $refId;
        return $this;
    }
    /**
     * getter for code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }
    /**
     * Setter for code
     *
     * @return Unit
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }
    /**
     * getter for year
     *
     * @return int
     */
    public function getYear()
    {
        return $this->year;
    }
    /**
     * Setter for year
     *
     * @return Unit
     */
    public function setYear($year)
    {
        $this->year = $year;
        return $this;
    }
    
    /**
     * (non-PHPdoc)
     * @see BaseEntity::__loadDaoMap()
     */
    public function __loadDaoMap()
    {
        DaoMap::begin($this, 'unit');
        DaoMap::setStringType('name', 'varchar', 100);
        DaoMap::setStringType('refId', 'varchar', 50);
        DaoMap::setStringType('code', 'varchar', 25);
        DaoMap::setIntType('year', 'int', 2, true, true);
        
        parent::__loadDaoMap();
        
        DaoMap::createIndex('name');
        DaoMap::createIndex('refId');
        DaoMap::createIndex('code');
        DaoMap::createIndex('year');
        DaoMap::commit();
    }
     /**
     * (non-PHPdoc)
     * @see BaseEntityAbstract::postSave()
     */
    public function postSave() 
    {
    	if(trim($this->getRefId()) !== '')
    		UnitConnector::sync($this);
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
    		$topics = $this->getTopics();
    		$array['topics'] = (count($topics) === 0 ? array() : (array_map(create_function('$a', 'return $a->getJson();'), $topics)) );
    	}
    	return parent::getJson($array, $reset);
    }
    /**
     * create a new Unit
     *
     * @param string $name
     * @param string $code
     * @param int	 $year
     * @param bool	 $active
     * 
     * @throws Exception
     * @return Unit
     */
    public static function create($name, $code, $refId = '', $year = null, $active = true)
    {
    	if(($name = trim($name)) === '')
    		throw new Exception('Name cannot be empty to create a new ' . __CLASS__);
    	if(($code = strtolower(trim($code))) === '')
    		throw new Exception('Code cannot be empty to create a new ' . __CLASS__);
    	$refId = trim($refId);
    	$year = $year === null ? null : intval($year);
    	$active = (intval($active) === 1);
    	
    	if($refId !== '' && ($obj = self::getByRefId($refId, false)) instanceof self)
    		$obj = $obj;
    	elseif(($obj = self::getByCode($code, false)) instanceof self)
    	$obj = $obj;
    	else $obj = new self();
    	
    	$obj->setName($name)
    		->setRefId($refId)
    		->setCode($code)
    		->setYear($year)
	    	->setActive($active)
	    	->save();
    	return $obj;
    }
    public static function getByRefId($refId, $activeOnly = true)
    {
    	$refId = trim($refId);
    	$activeOnly = (intval($activeOnly) === 1);
    	$objs = self::getAllByCriteria('refId = ?', array($refId), $activeOnly, 1, 1);
    	return count($objs) > 0 ? $objs[0] : null;
    }
    /**
     * get Unit by code
     *
     * @param string $code
     * @param bool	 $activeOnly
     * 
     * @return Unit|null
     */
    public static function getByCode($code, $activeOnly = true)
    {
    	$code = trim($code);
    	$activeOnly = (intval($activeOnly) === 1);
    	$objs = self::getAllByCriteria('code like ?', array($code), $activeOnly, 1, 1);
    	return count($objs) > 0 ? $objs[0] : null;
    }
    public function clearTopics()
    {
    	Unit_Topic::deleteByCriteria('unitId = :uId', array('uId' => $this->getId()));
    	return $this;
    }
    /**
     * get topics for this Unit
     *
     * @param bool $activeOnly
     *
     * @return array Topic
     */
    public function getTopics($activeOnly = true)
    {
    	$activeOnly = (intval($activeOnly) === 1);
    	$result = array();
    	$objs = Unit_Topic::getByUnitAndTopic($this, null, $activeOnly);
    	foreach ($objs as $obj)
    		$result[] = $obj->getTopic();
    	return $result;
    }
    /**
     * add a Topic to Unit 
     *
     * @param Topic $topic
     * @param bool  $active
     *
     * @return Unit
     */
    public function addTopic(Topic $topic, $active = true)
    {
    	$active = (intval($active) === 1);
    	$obj = Unit_Topic::create($this, $topic, $active);
    	return $this;
    }
}
?>
