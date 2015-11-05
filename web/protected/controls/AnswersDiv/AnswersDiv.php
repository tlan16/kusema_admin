<?php
/**
 * The AnswersDiv Loader
 *
 * @package    web
 * @subpackage controls
 * @author     flan<franklan118@gmail.com>
 */
class AnswersDiv extends TTemplateControl
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
					$this->getPage()->getClientScript()->registerScriptFile('AnswersDiv.Js', $this->publishAsset($value));
				else if($key === 'css')
					$this->getPage()->getClientScript()->registerStyleSheetFile('AnswersDiv.css', $this->publishAsset($value),'screen');
			}
		}
	}

	public function onLoad($param)
	{
		parent::onLoad($param);
		if(!$this->getPage()->IsCallBack && !$this->getPage()->IsPostBack)
		{
			$js = 'AnswersDivJs.UPDATE_BTN_ID = "' . $this->updateAnswerBtn->getUniqueID() . '";';
			$this->getPage()->getClientScript()->registerEndScript('AnswersDivJS', $js);
		}
	}
	/**
	 *
	 * @param unknown $sender
	 * @param unknown $params
	 */
	public function updateAnswer($sender, $params)
	{
		$results = $errors = array();
		try
		{
			Dao::beginTransaction();
			var_dump($params->CallbackParameter);
			if(!isset($params->CallbackParameter->entityName) || ($entityName = trim($params->CallbackParameter->entityName)) === '')
				throw new Exception('System Error: EntityName is not provided!');
			if(!isset($params->CallbackParameter->entityId) || ($entityId = trim($params->CallbackParameter->entityId)) === '')
				throw new Exception('System Error: entityId is not provided!');
			if(!($entity = $entityName::get($entityId)) instanceof $entityName)
				throw new Exception('System Error: entity is not provided!');
				
			if(!isset($params->CallbackParameter->answerId) || ($answerId = trim($params->CallbackParameter->answerId)) === '')
				throw new Exception('System Error: invalid answer id passed in!');
			
			if(!isset($params->CallbackParameter->value))
				throw new Exception('System Error: invalid value passed in!');
			$value = trim($params->CallbackParameter->value);
			if($answerId === 'new' && $value === '')
				throw new Exception('System Error: cannot create empty comments passed in!');
			if($answerId !== 'new' && !($answer = Answer::get($answerId)) instanceof Answer )
				throw new Exception('System Error: invalid answerId passed in!');
			if($answerId === 'new')
				$answer = Answer::create("", $value, $entity, null, Core::getUser()->getPerson());
			elseif($answer instanceof Answer)
			{
				if(trim($value) === '')
					$answer->setActive(false);
				else $answer->setContent($value);
				$answer->save();
			}
			
			$results['item'] = $answer->getJson();
			if($answer instanceof Answer)
				AnswerConnector::sync($answer);
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