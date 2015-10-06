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
	public $menuItem = 'topic.detail';
	/**
	 * (non-PHPdoc)
	 * @see BPCPageAbstract::$_focusEntityName
	 */
	protected $_focusEntity = 'Topic';
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
				,'refId' => 'refId_div'
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
			$focusEntity = $this->getFocusEntity();
			Dao::beginTransaction();
			if (! isset ( $params->CallbackParameter->entityName ) || ($entityName = trim ( $params->CallbackParameter->entityName )) === '')
				$entityName = $focusEntity;
			if (!isset ( $params->CallbackParameter->entityId ) || ($entityId = trim ( $params->CallbackParameter->entityId )) === '')
				throw new Exception ( 'System Error: entityId is not provided!' );
			if ($entityId !== 'new' && ! ($entity = $entityName::get ( $entityId )) instanceof $entityName)
				throw new Exception ( 'System Error: no such a entity exisits!' );
			if ($entityId !== 'new' && ( !isset ( $params->CallbackParameter->field ) || ($field = trim ( $params->CallbackParameter->field ))  === '') )
				throw new Exception ( 'System Error: invalid field passed in!' );
			if (! isset ( $params->CallbackParameter->value ))
				throw new Exception ( 'System Error: invalid value passed in!' );
			$value = $params->CallbackParameter->value;
			switch ($entityName)
			{
				case $focusEntity: {
					if($entityId === 'new') {
						if (!isset ( $value->name ) || ($name = trim ( $value->name )) === '')
							throw new Exception ( 'System Error: name is not provided!' );
						if (!isset ( $value->refId ) || ($refId = trim ( $value->refId )) === '')
							$refId = '';
						$entity = $focusEntity::create($name, $refId);
						break;
					}
					switch ($field)
					{
						case 'name': {
							if(($value = trim($value)) === '')
								throw new Exception ( 'System Error: invalid name passed in!' );
							$entity->setName($value);
							break;
						}
						case 'refId': {
							$entity->setRefId(trim($value));
							break;
						}
					}
					break;
				}
			}
			
			$results ['item'] = $entity->save()->getJson ();
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
