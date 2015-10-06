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
	public $menuItem = 'question.detail';
	/**
	 * (non-PHPdoc)
	 * @see BPCPageAbstract::$_focusEntityName
	 */
	protected $_focusEntity = 'Question';
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
				'title' => 'title_div'
				,'author' => 'author_div'
				,'content' => 'content_div'
				,'topicsUnits' => 'topics_units_div'
				,'comments' => 'comments_div'
				,'newAnswer' => 'new_answers_btn_div'
				,'answers' => 'answers_div'
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
			Dao::beginTransaction();
			if (! isset ( $params->CallbackParameter->entityName ) || ($entityName = trim ( $params->CallbackParameter->entityName )) === '')
				throw new Exception ( 'System Error: EntityName is not provided!' );
			if (! isset ( $params->CallbackParameter->entityId ) || ($entityId = trim ( $params->CallbackParameter->entityId )) === '')
				throw new Exception ( 'System Error: entityId is not provided!' );
			if ($entityId !== 'new' && ! ($entity = $entityName::get ( $entityId )) instanceof $entityName)
				throw new Exception ( 'System Error: no such a entity exisits!' );
			if (! isset ( $params->CallbackParameter->field ) || ($field = trim ( $params->CallbackParameter->field )) === '')
				throw new Exception ( 'System Error: invalid field passed in!' );
			if (! isset ( $params->CallbackParameter->value ))
				throw new Exception ( 'System Error: invalid value passed in!' );
			$value = $params->CallbackParameter->value;
			switch ($entityName)
			{
				case 'Question': {
					switch ($field)
					{
						case 'title': {
							if(($title = trim($value)) === '')
								throw new Exception ( 'System Error: invalid title passed in!' );
							$entity->setTitle($title);
							break;
						}
						case 'alias': {
							$entity->setAuthorName(trim($value));
							break;
						}
						case 'author': {
							if(!($author = Person::get(intval($value))) instanceof Person)
								throw new Exception ( 'System Error: invalid author passed in!' );
							$entity->setAuthor($author);
							break;
						}
						case 'active': {
							$entity->setActive(intval($value)===1);
							break;
						}
						case 'content': {
							$entity->setContent(trim($value));
							break;
						}
						case 'answer': { // this only happens when creating new Answer for the Question
							$entity = $entity->addAnswer("", trim($value));
							//$entity will now become an instant of Answer
							break;
						}
					}
					break;
				}
				case 'Answer': {
					switch ($field)
					{
						case 'content': {
							$entity->setContent(trim($value));
							break;
						}
						case 'active': {
							$entity->setActive(intval($value)===1);
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
