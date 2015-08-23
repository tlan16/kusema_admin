<?php
/**
 * Unit_Topic Entity
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class Unit_Topic extends BaseEntityAbstract
{
	/**
	 * The Unit of the relationship
	 * 
	 * @var Unit
	 */
    protected $unit;
    /**
     * The Topic of the relationship
     * 
     * @var Topic
     */
    protected $topic;
    
    /**
     * getter for unit
     *
     * @return Unit
     */
    public function getUnit()
    {
    	$this->loadManyToOne('unit');
        return $this->unit;
    }
    /**
     * Setter for unit
     * 
     * @param Unit $unit
     * @return Unit_Topic
     */
    public function setUnit($unit)
    {
        $this->unit = $unit;
        return $this;
    }
    /**
     * getter for topic
     *
     * @return Topic
     */
    public function getTopic()
    {
    	$this->loadManyToOne('topic');
        return $this->topic;
    }
    /**
     * Setter for topic
     *
     * @param Topic $topic
     * @return Unit_Topic
     */
    public function setTopic($topic)
    {
        $this->topic = $topic;
        return $this;
    }
    
    /**
     * (non-PHPdoc)
     * @see BaseEntity::__loadDaoMap()
     */
    public function __loadDaoMap()
    {
        DaoMap::begin($this, 'unit_tpc');
        DaoMap::setManyToOne('unit', 'Unit');
        DaoMap::setManyToOne('topic', 'Topic');
        
        parent::__loadDaoMap();
        
        DaoMap::commit();
    }
    /**
     * create a new unit_topic relationship
     * 
     * @param Unit 	$unit
     * @param Topic $topic
     * @param bool	$active
     * 
     * @return Unit_Topic
     */
    public static function create(Unit $unit, Topic $topic, $active = true)
    {
    	$active = (intval($active) === 1);
    	$obj = self::getByUnitAndTopic($unit, $topic);
    	$obj = $obj instanceof self ? $obj : new self();
    	$obj->setUnit($unit)
    		->setTopic($topic)
    		->setActive($active)
    		->save();
    	return $obj;
    }
    /**
     * get Unit_Topic relationship by Unit and Topic
     * 
     * @param Unit|null 	$unit
     * @param Topic|null 	$topic
     * @param bool 			$activeOnly
     * 
     * @return Unit_Topic|null
     */
    public static function getByUnitAndTopic($unit = null, $topic = null, $activeOnly = true)
    {
    	if($unit === null && $topic === null)
    		throw new Exception('one of Unit or Topic must given');
    	$activeOnly = (intval($activeOnly) === 1);
    	$criteria = '';
    	$param = array();
    	if($unit instanceof Unit)
    	{
    		$criteria .= 'unitId = ?';
    		$param[] = $unit->getId();
    	}
    	if($topic instanceof Topic)
    	{
    		$criteria .= (trim($criteria) === '' ? '' : ' and ') . 'topicId = ?';
    		$param[] = $topic->getId();
    	}
    	$objs = self::getAllByCriteria($criteria, $param, $activeOnly, 1, 1);
    	return count($objs) > 0 ? $objs[0] : null;
    }
}
?>
