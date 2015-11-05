<?php
/**
 * UserAccount Entity
 *
 * @package    Core
 * @subpackage Entity
 * @author     flan<franklan118@gmail.com>
 */
class UserAccount extends BaseEntityAbstract
{
    /**
     * The id of the system account
     *
     * @var int
     */
    const ID_SYSTEM_ACCOUNT = 10;
    const SOURCE_FORUM_KUSEMA = 'kusema';
    /**
     * The username
     *
     * @var string
     */
    private $username;
    /**
     * The password
     *
     * @var string
     */
    private $password;
    /**
     * The person
     *
     * @var Person
     */
    protected $person;
    /**
     * The source
     * 
     * @var string
     */
    private $source = null;
    /**
     * Thre ref id
     * 
     * @var string
     */
    private $refId = null;
    /**
     * getter UserName
     *
     * @return String
     */
    public function getUserName()
    {
        return $this->username;
    }
    /**
     * Setter UserName
     *
     * @param String $UserName The username
     *
     * @return UserAccount
     */
    public function setUserName($UserName)
    {
        $this->username = $UserName;
        return $this;
    }
    /**
     * getter Password
     *
     * @return String
     */
    public function getPassword()
    {
        return $this->password;
    }
    /**
     * Setter Password
     *
     * @param string $Password The password
     *
     * @return UserAccount
     */
    public function setPassword($Password)
    {
        $this->password = $Password;
        return $this;
    }
    /**
     * getter Person
     *
     * @return Person
     */
    public function getPerson()
    {
        $this->loadManyToOne("person");
        return $this->person;
    }
    /**
     * Setter Person
     *
     * @param Person $Person The person that this useraccount belongs to
     *
     * @return UserAccount
     */
    public function setPerson(Person $Person)
    {
        $this->person = $Person;
        return $this;
    }
    /**
     * getter for source
     *
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }
    /**
     * Setter for source
     *
     * @return UserAccount
     */
    public function setSource($source)
    {
        $this->source = $source;
        return $this;
    }
    /**
     * getter for refId
     *
     * @return sting | null
     */
    public function getRefId()
    {
        return $this->refId;
    }
    /**
     * Setter for refId
     *
     * @return UserAccount
     */
    public function setRefId($refId)
    {
        $this->refId = $refId;
        return $this;
    }
    /**
     * (non-PHPdoc)
     * @see BaseEntity::__toString()
     */
    public function __toString()
    {
        return $this->getUserName();
    }
    /**
     * (non-PHPdoc)
     * @see BaseEntityAbstract::get()
     * 
     * @return UserAccount
     */
    public static function get($id)
    {
    	return parent::get($id);
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
    		$array['person'] = $this->getPerson()->getJson();
    		$array['roles'] = array();
    		foreach($this->getRoles() as $role)
    			$array['roles'][] = $role->getJson();
    	}
    	$array = parent::getJson($array, $reset);
    	unset($array['password']);
    	return $array;
    }
    /**
     * (non-PHPdoc)
     * @see BaseEntity::__loadDaoMap()
     */
    public function __loadDaoMap()
    {
        DaoMap::begin($this, 'ua');
        DaoMap::setStringType('username', 'varchar', 100);
        DaoMap::setStringType('password', 'varchar', 64);
        DaoMap::setManyToOne("person", "Person", "p");
        DaoMap::setStringType('source', 'varchar', 10, true, null);
        DaoMap::setStringType('refId', 'varchar', 50, true, null);
        parent::__loadDaoMap();

        DaoMap::createUniqueIndex('username');
        DaoMap::createIndex('password');
        DaoMap::createIndex('source');
        DaoMap::createIndex('refId');
        DaoMap::commit();
    }
    /**
     * create a new user account
     * 
     * @param string 		$username
     * @param string 		$password
     * @param Person 		$person
     * @param string|null 	$source
     * @param string|null 	$refId
     * 
     * @return UserAccount
     * @throws Exception
     */
    public static function create($username, $password, Person $person, $role = null, $source = null, $refId = null)
    {
    	if(($username = trim($username)) === '')
    		throw new Exception('invalid username passed in');
    	if(($password = trim($password)) === '')
    		throw new Exception('invalid password passed in');
    	if($refId !== null && ($refId = trim($refId)) === '')
    		throw new Exception('invalid ref passed in');
    	$password = hash('sha256', $password);
    	$where = '';
    	$param = array();
    	if($source === null)
    		$where .= " source IS NULL AND ";
    	elseif(($source = trim($source)) !== '')
    	{
    		$where .= " source = :source AND ";
    		$param['source'] = $source;
    	}
    	else $source = null;
    	$where .= " username = :uname";
    	$param['uname'] = $username;
    	$objs = self::getAllByCriteria($where, $param, false, 1, 1);
    	$obj = (count($objs) > 0 ? $objs[0] : new self() );
    	$obj->setUserName($username)
    		->setPassword($password)
    		->setPerson($person)
    		->setSource($source)
    		->setRefId($refId)
    		->setActive(true)
    		->save();
    	if($role instanceof Role)
    		$obj->addRole($role);
    	return $obj;
    }
    /**
     * Getting UserAccount
     *
     * @param string  $username The username string
     * @param string  $password The password string
     *
     * @throws AuthenticationException
     * @throws Exception
     * @return Ambigous <BaseEntityAbstract>|NULL
     */
    public static function getUserByUsernameAndPassword($username, $password, $noHashPass = false, $localOnly = true)
    {
    	$userAccounts = self::getAllByCriteria( ($localOnly === true ? "`source` is NULL AND " : "") . "`UserName` = :username AND `Password` = :password", array('username' => $username, 'password' => ($noHashPass === true ? $password : hash('sha256', $password))), true, 1, 2);
    	if(count($userAccounts) === 1)
    		return $userAccounts[0];
    	else if(count($userAccounts) > 1)
    		throw new AuthenticationException("Multiple Users Found!Contact you administrator!");
    	else
    		throw new AuthenticationException("No User Found!");
    }
    /**
     * Getting UserAccount by username
     *
     * @param string $username    The username string
     *
     * @throws AuthenticationException
     * @throws Exception
     * @return Ambigous <BaseEntityAbstract>|NULL
     */
    public static function getUserByUsername($username, $localOnly = true)
    {
    	$userAccounts = self::getAllByCriteria( ($localOnly === true ? "`source` is NULL AND " : "") . "`UserName` = :username", array('username' => $username), true, 1, 2);
    	if(count($userAccounts) === 1)
    		return $userAccounts[0];
    	else if(count($userAccounts) > 1)
    		throw new AuthenticationException("Multiple Users Found!Contact you administrator!");
    	else
    		throw new AuthenticationException("No User Found!");
    }
    /**
     * getter Roles
     *
     * @return array Role
     */
    public function getRoles()
    {
    	return UserProfile::getRolesByUserAccount($this);
    }
    /**
     * setter Roles
     *
     * @param array $Roles The roles that this user has
     *
     * @return UserAccount
     */
    public function setRoles(array $Roles)
    {
    	foreach ($Roles as $role)
    	{
    		if(!$role instanceof Role)
    			continue;
    		UserProfile::create($this, UserProfileType::get(UserProfileType::ID_ROLE), $role);
    	}
    	return $this;
    }
    /**
     * Clear all the roles
     *
     * @return UserAccount
     */
    public function clearRoles()
    {
    	UserProfile::clearRolesByUserAccount($this);
    	return $this;
    }
    /**
     * Adding a role
     *
     * @param Role $role
     *
     * @return UserAccount
     */
    public function addRole(Role $role)
    {
    	UserProfile::addRoleByUserAccount($this, $role);
    	return $this;
    }
    /**
     * Deleting the role
     *
     * @param Role $role
     *
     * @return UserAccount
     */
    public function removeRole(Role $role)
    {
    	UserProfile::removeRoleByUserAccount($this, $role);
    	return $this;
    }
}

?>
