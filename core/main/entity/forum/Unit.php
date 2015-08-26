<?php
/**
 * Unit Entity
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class Unit extends BaseEntityAbstract
{
    /**
     * The name of the Unit
     * @var string
     */
    private $name;
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
        DaoMap::setStringType('code', 'varchar', 25);
        DaoMap::setIntType('year', 'int', 2, true, true);
        
        parent::__loadDaoMap();
        
        DaoMap::createIndex('name');
        DaoMap::createIndex('code');
        DaoMap::createIndex('year');
        DaoMap::commit();
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
    public static function create($name, $code, $year = null, $active = true)
    {
    	if(($name = trim($name)) === '')
    		throw new Exception('Name cannot be empty to create a new ' . __CLASS__);
    	if(($code = strtoupper(trim($code))) === '')
    		throw new Exception('Code cannot be empty to create a new ' . __CLASS__);
    	$year = $year === null ? null : intval($year);
    	$active = (intval($active) === 1);
    	$obj = self::getByCode($code);
    	$obj = $obj instanceof self ? $obj : new self();
    	$obj->setName($name)
    		->setCode($code)
    		->setYear($year)
	    	->setActive($active)
	    	->save();
    	return $obj;
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
    	$objs = Unit_Topic::getByUnitAndTopic($this, null, $activeOnly);
    	return $objs;
    }
    /**
     * add a Topic to Unit 
     *
     * @param Topic $topic
     * @param bool  $active
     *
     * @return Unit
     */
    public function addUnit(Topic $topic, $active = true)
    {
    	$active = (intval($active) === 1);
    	$obj = Unit_Topic::create($this, $topic, $active);
    	return $this;
    }
}
?>