<?php
/**
 * The CommentsDiv Loader
 *
 * @package    web
 * @subpackage controls
 * @author     flan<franklan118@gmail.com>
 */
class CommentsDiv extends TTemplateControl
{
	public function onInit($param)
	{
		parent::onInit($param);

		$scriptArray = BPCPageAbstract::getLastestJS(get_class($this));
		foreach($scriptArray as $key => $value)
		{
			if(($value = trim($value)) !== '')
			{
				if($key === 'js')
					$this->getPage()->getClientScript()->registerScriptFile('CommentsDiv.Js', $this->publishAsset($value));
				else if($key === 'css')
					$this->getPage()->getClientScript()->registerStyleSheetFile('CommentsDiv.css', $this->publishAsset($value),'screen');
			}
		}
	}

	public function onLoad($param)
	{
		parent::onLoad($param);
		if(!$this->getPage()->IsCallBack && !$this->getPage()->IsPostBack)
		{
			$js = 'CommentsDivJs.UPDATE_BTN_ID = "' . $this->updateCommentsBtn->getUniqueID() . '";';
			$this->getPage()->getClientScript()->registerEndScript('CommentsDivJS', $js);
		}
	}
	/**
	 *
	 * @param unknown $sender
	 * @param unknown $params
	 */
	public function updateComments($sender, $params)
	{
		$results = $errors = array();
		try
		{
			Dao::beginTransaction();
			if(!isset($params->CallbackParameter->entityName) || ($entityName = trim($params->CallbackParameter->entityName)) === '')
				throw new Exception('System Error: EntityName is not provided!');
			if(!isset($params->CallbackParameter->entityId) || ($entityId = trim($params->CallbackParameter->entityId)) === '')
				throw new Exception('System Error: entityId is not provided!');
			if(!($entity = $entityName::get($entityId)) instanceof $entityName)
				throw new Exception('System Error: no such a entity exisits!');
			if(!isset($params->CallbackParameter->commentsId) || ($commentsId = trim($params->CallbackParameter->commentsId)) === '')
				throw new Exception('System Error: invalid comments passed in!');
			if(!isset($params->CallbackParameter->value))
				throw new Exception('System Error: invalid value passed in!');
			$value = $params->CallbackParameter->value;
			if($commentsId === 'new' && $value === '')
				throw new Exception('System Error: cannot create empty comments passed in!');
			if($commentsId !== 'new' && !($comments = Comments::get($commentsId)) instanceof Comments)
				throw new Exception('System Error: invalid commentsId passed in!');
			if($commentsId === 'new')
				$comments = Comments::create("", $value, $entity, null, Core::getUser()->getPerson());
			elseif($comments instanceof Comments)
			{
				if(trim($value) === '')
					$comments->setActive(false);
				else $comments->setContent($value);
				$comments->save();
			}

			$results['item'] = $comments->getJson();
			if($comments instanceof Comments)
				CommentsConnector::sync($comments);
			Dao::commitTransaction();
		}
		catch(Exception $ex)
		{
			Dao::rollbackTransaction();
			$errors[] = $ex->getMessage();
		}
		$params->ResponseData = StringUtilsAbstract::getJson($results, $errors);
	}
}