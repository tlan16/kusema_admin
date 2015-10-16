<?php
/**
 * Person Entity
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class Person extends BaseEntityAbstract
{
    /**
     * The firstname of the person
     * @var string
     */
    private $firstName;
    /**
     * The lastname of the person
     * @var string
     */
    private $lastName;
    /**
     * The email of the person
     * 
     * @var string
     */
    private $email;
    /**
     * The useraccounts of the person
     * @var array
     */
    protected $userAccounts;
    /**
     * The ref id of the Person
     * 
     * @var sring
     */
    private $refId = "";
    /**
     * getter UserAccount
     *
     * @return UserAccount
     */
    public function getUserAccounts()
    {
        $this->loadOneToMany('userAccounts');
        return $this->userAccounts;
    }
    /**
     * Setter UserAccount
     *
     * @param array $userAccounts The useraccounts
     *
     * @return Person
     */
    public function setUserAccounts(array $userAccounts)
    {
        $this->userAccounts = $userAccounts;
        return $this;
    }
     
    /**
     * getter FirstName
     *
     * @return String
     */
    public function getFirstName()
    {
        return $this->firstName;
    }
    /**
     * Setter FirstName
     *
     * @param String FirstName The firstName of the person
     *
     * @return Person
     */
    public function setFirstName($FirstName)
    {
        $this->firstName = $FirstName;
        return $this;
    }
    /**
     * getter LastName
     *
     * @return String
     */
    public function getLastName()
    {
        return $this->lastName;
    }
    /**
     * Setter LastName
     *
     * @param String $LastName The last name
     *
     * @return Person
     */
    public function setLastName($LastName)
    {
        $this->lastName = $LastName;
        return $this;
    }
    /**
     * getter for email
     *
     * @return string
     */
    public function getEmail()
    {
    	return $this->email;
    }
    /**
     * Setter for email
     *
     * @return Person
     */
    public function setemail($email)
    {
    	$this->email = $email;
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
     * @return Person
     */
    public function setRefId($refId)
    {
        $this->refId = $refId;
        return $this;
    }
    
    /**
     * getting the fullname of the person
     *
     * @return string
     */
    public function getFullName()
    {
        $names = array();
        if(($firstName = trim($this->getFirstName())) !== '')
        	$names[] = $firstName;
        if(($lastName = trim($this->getLastName())) !== '')
        	$names[] = $lastName;
        return trim(implode(' ', $names));
    }
    /**
     * (non-PHPdoc)
     * @see BaseEntity::__toString()
     */
    public function __toString()
    {
        if(($name = $this->getFullName()) !== '')
            return $name;
        return parent::__toString();
    }
    /**
     * (non-PHPdoc)
     * @see BaseEntity::__loadDaoMap()
     */
    public function __loadDaoMap()
    {
        DaoMap::begin($this, 'p');
        DaoMap::setStringType('firstName');
        DaoMap::setStringType('lastName');
        DaoMap::setStringType('email', 'varchar', 100, true);
        DaoMap::setStringType('refId', 'varchar', 50, true);
        DaoMap::setOneToMany('userAccounts', 'UserAccount', 'ua');
        parent::__loadDaoMap();
        
        DaoMap::createIndex('firstName');
        DaoMap::createIndex('lastName');
        DaoMap::createIndex('email');
        DaoMap::createIndex('refId');
        DaoMap::commit();
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
    		$array['fullName'] = trim($this->getFullName());
    		$array['subscribedUnit'] = array_map(create_function('$a', 'return $a->getJson();'), $this->getSubscribedUnits());
    		$array['subscribedTopic'] = array_map(create_function('$a', 'return $a->getJson();'), $this->getSubscribedTopics());
    		$array['enrolledUnit'] = array_map(create_function('$a', 'return $a->getJson();'), $this->getEnrolleddUnits());
    		$array['enrolledTopic'] = array_map(create_function('$a', 'return $a->getJson();'), $this->getEnrolledTopics());
    	}
    	return parent::getJson($array, $reset);
    }
    /**
     * create a new Person
     * 
     * @param string $firstName
     * @param string $lastName
     * @param string $email
     * 
     * @return Person
     * @throws Exception
     */
    public static function create($firstName, $lastName, $email = null, $refId = null)
    {
    	if(($firstName = trim($firstName)) === '')
    		throw new Exception('Invalid firstName passed in');
    	if($email !== null && (trim($email) === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false))
    		throw new Exception('Invalid email passed in');
    	$email = ( ($email === null || trim($email) === '') ? null : trim($email));
    	$refId = ( ($refId === null || trim($refId) === '') ? null : trim($refId));
    	
    	if($refId !== null && ($obj = self::getByRefId($refId, false)) instanceof self)
    		$obj = $obj;
//     	if($email !== null && self::getByEmail($email, true) instanceof self)
//     		$obj = self::getByEmail($email);
//     	elseif(self::getByName($firstName, $lastName, true) instanceof self)
//     		$obj = self::getByName($firstName, $lastName, true);
    	else $obj = new self();
    	
    	$obj->setFirstName($firstName)
    		->setLastName($lastName)
    		->setemail($email)
    		->setRefId($refId)
    		->setActive(true)
    		->save();
    	return $obj;
    }
    public static function getByRefId($refId, $activeOnly = true)
    {
    	if(($refId = trim($refId)) === '')
    		return null;
    	$activeOnly = (intval($activeOnly) === 1);
    	$objs = self::getAllByCriteria('refId = ?', array($refId), $activeOnly, 1, 1);
    	return count($objs) > 0 ? $objs[0] : null;
    }
    public static function getByEmail($email, $activeOnly = true)
    {
    	if(($email = trim($email)) === '')
    		return null;
    	$activeOnly = (intval($activeOnly) === 1);
    	$objs = self::getAllByCriteria('email like ?', array($email), $activeOnly, 1, 1);
    	return count($objs) > 0 ? $objs[0] : null;
    }
	public static function getByName($firstName, $lastName, $activeOnly = true)
    {
    	if(($firstName = trim($firstName)) === '')
    		throw new Exception('Invalid firstName passed in');
    	$activeOnly = (intval($activeOnly) === 1);
    	$objs = self::getAllByCriteria('firstName like ? and lastName like ?', array($firstName, $lastName), $activeOnly, 1, 1);
    	return count($objs) > 0 ? $objs[0] : null;
    }
    
    public function clearSubscribeUnit() {
    	PersonProfile::deleteByCriteria('personId = :pId and typeId = :tId and entityName = :eName', array('pId' => $this->getId(), 'tId' => PersonProfileType::ID_SUBSCRIPTION, 'eName' => 'Unit'));
    	return $this;
    }
    public function subscribeUnit(Unit $unit) {
    	return PersonProfile::create($this, PersonProfileType::get(PersonProfileType::ID_SUBSCRIPTION), $unit);
    }
    public function getSubscribedUnits() {
    	$result = array();
    	$objs = PersonProfile::getAllByCriteria('personId = :pId and typeId = :tId and entityName = :eName', array('pId' => $this->getId(), 'tId' => PersonProfileType::ID_SUBSCRIPTION, 'eName' => 'Unit'));
    	foreach ($objs as $obj)
    	{
    		if( ($unit = Unit::get($obj->getEntityId())) instanceof Unit )
    		$result[] = $unit;
    	}
    	return $result;
    }
    
    public function clearSubscribeTopic() {
    	PersonProfile::deleteByCriteria('personId = :pId and typeId = :tId and entityName = :eName', array('pId' => $this->getId(), 'tId' => PersonProfileType::ID_SUBSCRIPTION, 'eName' => 'Topic'));
    	return $this;
    }
    public function subscribeTopic(Topic $topic) {
    	return PersonProfile::create($this, PersonProfileType::get(PersonProfileType::ID_SUBSCRIPTION), $topic);
    }
    public function getSubscribedTopics() {
    	$result = array();
    	$objs = PersonProfile::getAllByCriteria('personId = :pId and typeId = :tId and entityName = :eName', array('pId' => $this->getId(), 'tId' => PersonProfileType::ID_SUBSCRIPTION, 'eName' => 'Topic'));
    	foreach ($objs as $obj)
    	{
    		if( ($topic = Topic::get($obj->getEntityId())) instanceof Topic )
    		$result[] = $topic;
    	}
    	return $result;
    }
    
    public function clearEnrollUnit() {
    	PersonProfile::deleteByCriteria('personId = :pId and typeId = :tId and entityName = :eName', array('pId' => $this->getId(), 'tId' => PersonProfileType::ID_ENROLLMENT, 'eName' => 'Unit'));
    	return $this;
    }
    public function enrollUnit(Unit $unit) {
    	return PersonProfile::create($this, PersonProfileType::get(PersonProfileType::ID_ENROLLMENT), $unit);
    }
    public function getEnrolleddUnits() {
    	$result = array();
    	$objs = PersonProfile::getAllByCriteria('personId = :pId and typeId = :tId and entityName = :eName', array('pId' => $this->getId(), 'tId' => PersonProfileType::ID_ENROLLMENT, 'eName' => 'Unit'));
    	foreach ($objs as $obj)
    	{
    		if( ($unit = Unit::get($obj->getEntityId())) instanceof Unit )
    		$result[] = $unit;
    	}
    	return $result;
    }
    
    public function clearEnrollTopic() {
    	PersonProfile::deleteByCriteria('personId = :pId and typeId = :tId and entityName = :eName', array('pId' => $this->getId(), 'tId' => PersonProfileType::ID_ENROLLMENT, 'eName' => 'Topic'));
    	return $this;
    }
    public function enrollTopic(Topic $topic) {
    	return PersonProfile::create($this, PersonProfileType::get(PersonProfileType::ID_ENROLLMENT), $topic);
    }
    public function getEnrolledTopics() {
    	$result = array();
    	$objs = PersonProfile::getAllByCriteria('personId = :pId and typeId = :tId and entityName = :eName', array('pId' => $this->getId(), 'tId' => PersonProfileType::ID_ENROLLMENT, 'eName' => 'Topic'));
    	foreach ($objs as $obj)
    	{
    		if( ($topic = Topic::get($obj->getEntityId())) instanceof Topic )
    		$result[] = $topic;
    	}
    	return $result;
    }
    public function sync() {
    	PersonConnector::sync($this);
    	return $this;
    }
}

?>
