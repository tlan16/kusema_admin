<?php
/**
 * This is the Allergent details page
 *
 * @package    Web
 * @subpackage Controller
 * @author     flan<franklan118@gmail.com>
 */
class DetailsController extends DetailsPageAbstract
{
	/**
	 * (non-PHPdoc)
	 * @see BPCPageAbstract::$menuItem
	 */
	public $menuItem = 'useraccount.detail';
	/**
	 * (non-PHPdoc)
	 * @see BPCPageAbstract::$_focusEntityName
	 */
	protected $_focusEntity = 'UserAccount';
	/**
	 * constructor
	 */
	public function __construct()
	{
		parent::__construct();
	}
	/**
	 * Getting The end javascript
	 *
	 * @return string
	 */
	protected function _getEndJs()
	{
		$js = parent::_getEndJs();
		$js .= "pageJs.setPreData(" . json_encode(array()) . ");";
		$js .= "pageJs._containerIds=" . json_encode(array(
				'username' => 'username_div'
				,'firstname' => 'firstname_div'
				,'lastname' => 'lastname_div'
				,'password' => 'password_div'
				,'new_store_roles' => 'new_store_roles_div'
				,'store_roles' => 'store_roles_div'
				,'comments' => 'comments_div'
				,'saveBtn' => 'save_btn'
		)) . ";";
		$js .= "pageJs.load();";
		$js .= "pageJs.bindAllEventNObjects();";
		return $js;
	}
	/**
	 * save the items
	 *
	 * @param unknown $sender
	 * @param unknown $param
	 * @throws Exception
	 *
	 */
	public function saveItem($sender, $params)
	{
		$results = $errors = array();
		try
		{
			$focusEntity = $this->getFocusEntity ();
			
			if (! isset ( $params->CallbackParameter->username ) || ($username = trim ( $params->CallbackParameter->username )) === '')
				throw new Exception ( 'System Error: invalid username passed in.' );
			
			if (! isset ( $params->CallbackParameter->firstname ) || ($firstName = trim ( $params->CallbackParameter->firstname )) === '')
				throw new Exception ( 'System Error: invalid firstname passed in.' );
			
			if (! isset ( $params->CallbackParameter->lastname ) || ($lastName = trim ( $params->CallbackParameter->lastname )) === '')
				throw new Exception ( 'System Error: invalid lastname passed in.' );
			
			$password = '';
			if (isset ( $params->CallbackParameter->password ))
				$password = trim ( $params->CallbackParameter->password );
			
			$store_roles = array();
			if (isset ( $params->CallbackParameter->store_roles ) && is_array($tmp = $params->CallbackParameter->store_roles) )
				$store_roles = $tmp;
			if(!is_array($store_roles) || count($store_roles) === 0)
				throw new Exception ( 'System Error: at least one role must be given to the user account.' );
				
			if (isset ( $params->CallbackParameter->id ) && ! ($entity = $focusEntity::get ( intval ( $params->CallbackParameter->id ) )) instanceof $focusEntity)
				throw new Exception ( 'System Error: invalid id passed in.' );
			
			$transStarted = false;
			try {Dao::beginTransaction();} catch(Exception $e) {$transStarted = true;}
			
			if (! isset ( $entity ) || ! $entity instanceof $focusEntity)
			{
				if($password === '')
					throw new Exception ( 'System Error: invalid password passed in.' );
				$person = Person::create($firstName, $lastName);
				$roles = array();
				foreach ($store_roles as $store_role)
				{
					if (! isset ( $store_role->role ) || ! ($role = Role::get ( intval ( $store_role->role ) )) instanceof Role)
						throw new Exception ( 'Invalid role passed in' );
					$roles[] = $role;
				}
				if(count($roles) === 0)
					throw new Exception ( 'System Error: at least one role must be given to the user account.' );
				$entity = $focusEntity::create($username, $password, $person, $roles);
			} else {
				$entity->setUserName($username);
				$person = $entity->getPerson();
				if(!$person instanceof Person)
					throw new Exception('System Error: cannot find person for userAccount[' . $entity->getId() . ']');
				$person->setFirstName($firstName)->setLastName($lastName)->save();
				if(trim($password) !== '')
					$entity->setPassword($password);
			}
			
			$entity->clearRoles();
			$count = 0;
			foreach ($store_roles as $store_role)
			{
				if(!isset($store_role->role) || !($role = Role::get(intval($store_role->role))) instanceof Role )
					throw new Exception('Invalid role passed in');
				$entity->addRole($role);
				$count++;
			}
			if($count === 0)
				throw new Exception ( 'System Error: at least one role must be given to the user account.' );
			
			$results ['item'] = $entity->save ()->getJson ();
			if($transStarted === false)
				Dao::commitTransaction();
		}
		catch(Exception $ex)
		{
			if(isset($transStarted) && $transStarted === false)
				Dao::rollbackTransaction();
			$errors[] = $ex->getMessage();
		}
		$params->ResponseData = StringUtilsAbstract::getJson($results, $errors);
	}
}
?>
