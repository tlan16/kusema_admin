<?php
/**
 * This is the Question details page
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
	public $menuItem = 'unit.detail';
	/**
	 * (non-PHPdoc)
	 * @see BPCPageAbstract::$_focusEntityName
	 */
	protected $_focusEntity = 'Unit';
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
				'name' => 'name_div'
				,'code' => 'code_div'
				,'topics' => 'topics_div'
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
			if (! isset ( $params->CallbackParameter->name ) || ($name = trim ( $params->CallbackParameter->name )) === '')
				throw new Exception ( 'System Error: invalid name passed in.' );
			if (! isset ( $params->CallbackParameter->code ) || ($code = trim ( $params->CallbackParameter->code )) === '')
				throw new Exception ( 'System Error: invalid code passed in.' );
			$topicIds = array();
			if (isset ( $params->CallbackParameter->topics ) && ($tmp = trim($params->CallbackParameter->topics)) !== '' )
				$topicIds = explode(',', $tmp);
			if (isset ( $params->CallbackParameter->id ) && ! ($entity = $focusEntity::get ( intval ( $params->CallbackParameter->id ) )) instanceof $focusEntity)
				throw new Exception ( 'System Error: invalid id passed in.' );
			
			Dao::beginTransaction ();
			
			if (! isset ( $entity ) || ! $entity instanceof $focusEntity)
				$entity = $focusEntity::create ( $name , $code);
			else
				$entity->setName ( $name )->setCode ( $code );
			
			foreach ($topicIds as $topicId)
			{
				if(($topic = Topic::get(intval($topicId))) instanceof Topic)
					$entity->addTopic($topic);
			}
				
			$results ['item'] = $entity->save()->getJson ();
			Dao::commitTransaction ();
			if($entity instanceof Unit)
				UnitConnector::sync($entity);
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
