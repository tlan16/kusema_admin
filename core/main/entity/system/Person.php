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
     * @see BaseEntityAbstract::getJson()
     */
    public function getJson($extra = array(), $reset = false)
    {
    	$array = $extra;
    	if(!$this->isJsonLoaded($reset))
    	{
    		$array['fullname'] = trim($this->getFullName());
    	}
    	return parent::getJson($array, $reset);
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
        DaoMap::setOneToMany('userAccounts', 'UserAccount', 'ua');
        parent::__loadDaoMap();
        
        DaoMap::createIndex('firstName');
        DaoMap::createIndex('lastName');
        DaoMap::createIndex('email');
        DaoMap::commit();
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
    public static function create($firstName, $lastName, $email = null)
    {
    	if(($firstName = trim($firstName)) === '')
    		throw new Exception('Invalid firstName passed in');
    	if(($lastName = trim($lastName)) === '')
    		throw new Exception('Invalid lastName passed in');
    	if($email !== null && (trim($email) === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false))
    		throw new Exception('Invalid email passed in');
    	$email = ( ($email === null || trim($email) === '') ? null : trim($email));
    	
    	if($email !== null && self::getByEmail($email, true) instanceof self)
    		$obj = self::getByEmail($email);
    	elseif(self::getByName($firstName, $lastName, true) instanceof self)
    		$obj = self::getByName($firstName, $lastName, true);
    	else $obj = new self();
    	
    	$obj->setFirstName($firstName)
    		->setLastName($lastName)
    		->setemail($email)
    		->setActive(true)
    		->save();
    	return $obj;
    }
    public static function getByEmail($email, $activeOnly = true)
    {
    	if(($email = trim($email)) === '')
    		throw new Exception('Invalid email passed in');
    	$activeOnly = (intval($activeOnly) === 1);
    	$objs = self::getAllByCriteria('email like ?', array($email), $activeOnly, 1, 1);
    	return count($objs) > 0 ? $objs[0] : null;
    }
	public static function getByName($firstName, $lastName, $activeOnly = true)
    {
    	if(($firstName = trim($firstName)) === '')
    		throw new Exception('Invalid firstName passed in');
    	if(($lastName = trim($lastName)) === '')
    		throw new Exception('Invalid lastName passed in');
    	$activeOnly = (intval($activeOnly) === 1);
    	$objs = self::getAllByCriteria('firstName like ? and lastName like ?', array($firstName, $lastName), $activeOnly, 1, 1);
    	return count($objs) > 0 ? $objs[0] : null;
    }
    public function subscribeUnit(Unit $unit) {
    	return PersonProfile::create($this, PersonProfileType::get(PersonProfileType::ID_SUBSCRIPTION), $unit);
    }
    public function subscribeTopic(Topic $topic) {
    	return PersonProfile::create($this, PersonProfileType::get(PersonProfileType::ID_SUBSCRIPTION), $topic);
    }
    public function enrollUnit(Unit $unit) {
    	return PersonProfile::create($this, PersonProfileType::get(PersonProfileType::ID_ENROLLMENT), $unit);
    }
    public function enrollTopic(Topic $topic) {
    	return PersonProfile::create($this, PersonProfileType::get(PersonProfileType::ID_ENROLLMENT), $topic);
    }
}

?>
