<?php
/**
 * Statics Entity
 *
 * @package    Core
 * @subpackage Entity
 * @author     flan<franklan118@gmail.com>
 */
class Statics extends BaseEntityAbstract
{
	const LOG_ROTATION = 3; // days
	const STATUS_PENDING = 1;
	
    /**
     * The entity of the Statics
     * @var string
     */
    private $entity;
    /**
     * The type of the Statics
     * @var string
     */
    private $type;
    /**
     * The action of the Statics
     * @var string
     */
    private $action;
    /**
     * The data of the Statics
     * @var string
     */
    private $data;
    /**
     * The status of the Statics 
     * @var int
     */
    private $status;
    
    /**
     * getter for entity
     *
     * @return string
     */
    public function getEntity()
    {
        return $this->entity;
    }
    /**
     * Setter for entity
     *
     * @return Statics
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
        return $this;
    }
    /**
     * getter for type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
    /**
     * Setter for type
     *
     * @return Statics
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }
    /**
     * getter for action
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }
    /**
     * Setter for action
     *
     * @return Statics
     */
    public function setAction($action)
    {
        $this->action = $action;
        return $this;
    }
    /**
     * getter for data
     *
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }
    /**
     * Setter for data
     *
     * @return Statics
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }
    /**
     * getter for status
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }
    /**
     * Setter for status
     *
     * @return Statics
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }
    
    /**
     * (non-PHPdoc)
     * @see BaseEntity::__loadDaoMap()
     */
    public function __loadDaoMap()
    {
        DaoMap::begin($this, 'stat');
        DaoMap::setStringType('entity', 'varchar', 25);
        DaoMap::setStringType('type', 'varchar', 25);
        DaoMap::setStringType('action', 'varchar', 25);
        DaoMap::setStringType('data', 'longtext');
        Daomap::setIntType('status', 'int', 1);
        
        parent::__loadDaoMap();
        
        DaoMap::createIndex('entity');
        DaoMap::createIndex('type');
        DaoMap::createIndex('action');
        DaoMap::createIndex('status');
        DaoMap::commit();
    }
    /**
     * Create a new Statics
     * 
     * @param string $entity
     * @param string $type
     * @param string $action
     * @param array	 $data
     * @param bool	 $active
     * 
     * @return Statics
     * @throws Exception
     */
    public static function create($entity, $type, $action, $data = "[]", $status = 0, $active = true)
    {
    	if(($entity = trim($entity)) === '')
    		throw new Exception('Entity cannot be empty to create a new ' . __CLASS__);
    	if(($type = trim($type)) === '')
    		throw new Exception('Type cannot be empty to create a new ' . __CLASS__);
    	if(($action = trim($action)) === '')
    		throw new Exception('Action cannot be empty to create a new ' . __CLASS__);
    	$active = (intval($active) === 1);
    	$status = intval($status);
    	$obj = self::getStatics($entity, $type, $action);
    	$obj = ($obj instanceof self ? $obj : new self());
    	$obj->setEntity($entity)
    		->setType($type)
    		->setAction($action)
    		->setData($data)
    		->setStatus($status === 0 ? (count($data) === 0 ? self::STATUS_PENDING : 0) : $status)
    		->setActive($active)
    		->save();
    	return $obj;
    }
    /**
     * get recent statics within a day
     * 
     * @param string $entity
     * @param string $type
     * @param string $action
     * @param bool	 $active
     * 
     * @return Statics|null
     * @throws Exception
     */
    public static function getStatics($entity, $type, $action, $active = true, $status = null)
    {
    	if(($entity = trim($entity)) === '')
    		throw new Exception('Entity cannot be empty to create a new ' . __CLASS__);
    	if(($type = trim($type)) === '')
    		throw new Exception('Type cannot be empty to create a new ' . __CLASS__);
    	if(($action = trim($action)) === '')
    		throw new Exception('Action cannot be empty to create a new ' . __CLASS__);
    	$date = UDate::now();
    	$date->setTime(0, 0, 0)->modify('-1 day');
    	$where = 'entity = :entity and type = :type and action = :action and created > :date';
    	$param = array('entity'=>$entity,'type'=>$type,'action'=>$action,'date'=>trim($date));
    	if($status !== null)
    	{
    		$where .= ' and status = :status';
    		$param = array_merge($param, array('status'=>intval($status)));
    	}
    	$objs = self::getAllByCriteria($where, $param, (intval($active)===1), 1, 1);
    	return (count($objs) > 0 ? $objs[0] : null);
    }
}
?>
