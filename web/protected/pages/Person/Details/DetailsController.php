<?php
/**
 * This is the Question details page
 *
 * @package    Web
 * @subpackage Controller
 * @author     lhe<helin16@gmail.com>
 */
class DetailsController extends DetailsPageAbstract
{
	/**
	 * (non-PHPdoc)
	 * @see BPCPageAbstract::$menuItem
	 */
	public $menuItem = 'person.detail';
	/**
	 * (non-PHPdoc)
	 * @see BPCPageAbstract::$_focusEntityName
	 */
	protected $_focusEntity = 'Person';
	/**
	 * constructor
	 */
	public function __construct()
	{
		parent::__construct();
		if(!AccessControl::canAccessQuestionDetailsPage(Core::getRole()))
			die('You do NOT have access to this page');
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
				'firstName' => 'firstName_div'
				,'lastName' => 'lastName_div'
				,'email' => 'email_div'
				,'subscribedUnit' => 'subscribed_unit_div'
				,'subscribedTopic' => 'subscribed_topic_div'
				,'enrolledUnit' => 'enrolled_unit_div'
				,'enrolledTopic' => 'enrolled_topic_div'
				,'active' => 'active_div'
				,'comments' => 'comments_div'
				,'saveBtn' => 'save_btn'
		)) . ";";
		$js .= "pageJs.load();";
		$js .= "pageJs.bindAllEventNObjects();";
		if(!AccessControl::canEditQuestionDetailsPage(Core::getRole()))
			$js .= "pageJs.readOnlyMode();";
		return $js;
	}
	public function saveItem($sender, $params)
	{
	
		$results = $errors = array();
		try
		{
			$focusEntity = $this->getFocusEntity ();
			if (! isset ( $params->CallbackParameter->firstName ) || ($firstName = trim ( $params->CallbackParameter->firstName )) === '')
				throw new Exception ( 'System Error: invalid firstName passed in.' );
			
			$lastName = '';
			if (isset ( $params->CallbackParameter->lastName ) && ($tmp = trim ( $params->CallbackParameter->lastName )) !== '')
				$lastName = $tmp;
			
			$email = '';
			if (isset ( $params->CallbackParameter->email ) && ($tmp = trim ( $params->CallbackParameter->email )) !== '')
				$email = $tmp;
			
			$subscribedUnitIds = array ();
			if (isset ( $params->CallbackParameter->subscribedUnit ) && ($tmp = trim ( $params->CallbackParameter->subscribedUnit )) !== '')
				$subscribedUnitIds = explode ( ',', $tmp );
			
			$subscribedTopicIds = array ();
			if (isset ( $params->CallbackParameter->subscribedTopic ) && ($tmp = trim ( $params->CallbackParameter->subscribedTopic )) !== '')
				$subscribedTopicIds = explode ( ',', $tmp );
			
			$enrolledUnitIds = array ();
			if (isset ( $params->CallbackParameter->enrolledUnit ) && ($tmp = trim ( $params->CallbackParameter->enrolledUnit )) !== '')
				$enrolledUnitIds = explode ( ',', $tmp );
			
			$enrolledTopicIds = array ();
			if (isset ( $params->CallbackParameter->enrolledTopic ) && ($tmp = trim ( $params->CallbackParameter->enrolledTopic )) !== '')
				$enrolledTopicIds = explode ( ',', $tmp );
			
			if (isset ( $params->CallbackParameter->id ) && ! ($entity = $focusEntity::get ( intval ( $params->CallbackParameter->id ) )) instanceof $focusEntity)
				throw new Exception ( 'System Error: invalid id passed in.' );
			
			Dao::beginTransaction ();
			
			if (! isset ( $entity ) || ! $entity instanceof $focusEntity)
				$entity = $focusEntity::create($firstName, $lastName, $email);
			else {
				$entity->setFirstName( $firstName )->setLastName( $lastName )->setemail($email);
			}
			
			$entity->clearEnrollTopic()->clearEnrollUnit()->clearSubscribeTopic()->clearSubscribeUnit();
			
			foreach ( $subscribedUnitIds as $subscribedUnitId )
			{
				if(($unit = Unit::get(intval($subscribedUnitId))) instanceof Unit)
					$entity->subscribeUnit($unit);
			}
			foreach ( $subscribedTopicIds as $subscribedTopicId )
			{
				if(($topic = Topic::get(intval($subscribedTopicId))) instanceof Topic)
					$entity->subscribeTopic($topic);
			}
			foreach ( $enrolledUnitIds as $enrolledUnitId )
			{
				if(($unit = Unit::get(intval($enrolledUnitId))) instanceof Unit)
					$entity->enrollUnit($unit);
			}
			foreach ( $enrolledTopicIds as $enrolledTopicId )
			{
				if(($topic = Topic::get(intval($enrolledTopicId))) instanceof Topic)
					$entity->enrollTopic($topic);
			}
			
			$results ['item'] = $entity->save ()->getJson ();
			Dao::commitTransaction ();
		}
		catch(Exception $ex)
		{
			Dao::rollbackTransaction();
			$errors[] = $ex->getMessage();
		}
		$params->ResponseData = StringUtilsAbstract::getJson($results, $errors);
	}
}
?>
