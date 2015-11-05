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
				,'authorName' => 'authorName_div'
				,'author' => 'author_div'
				,'content' => 'content_div'
				,'topicsUnits' => 'topics_units_div'
				,'comments' => 'comments_div'
				,'newAnswer' => 'new_answers_btn_div'
				,'answers' => 'answers_div'
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
			if (!isset ( $params->CallbackParameter->title ) || ($title = trim ( $params->CallbackParameter->title )) === '')
				throw new Exception ( 'System Error: invalid name passed in.' );
			$authorName = '';
			if (isset ( $params->CallbackParameter->authorName ) )
				$authorName = trim($params->CallbackParameter->authorName);
			if (!isset ( $params->CallbackParameter->author ) || ($authorId = intval( $params->CallbackParameter->author )) === 0 || !($author = Person::get($authorId)) instanceof Person)
				throw new Exception ( 'System Error: invalid author passed in.' );
			$content = '';
			if (isset ( $params->CallbackParameter->content ) )
				$content = trim($params->CallbackParameter->content);
			
			if (isset ( $params->CallbackParameter->id ) && !($entity = $focusEntity::get(intval($params->CallbackParameter->id))) instanceof $focusEntity )
				throw new Exception ( 'System Error: invalid id passed in.' );
			
			Dao::beginTransaction();
			
			if(!isset($entity) || !$entity instanceof $focusEntity)
				$entity = $focusEntity::create($name,$description);
			else $entity->setTitle($title)->setAuthorName($authorName)->setAuthor($author)->setContent($content);
			
			$results ['item'] = $entity->save()->getJson();
			Dao::commitTransaction ();
			if($entity instanceof Question && trim($entity->getRefId()) !== '')
				QuestionConnector::sync($entity);
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
