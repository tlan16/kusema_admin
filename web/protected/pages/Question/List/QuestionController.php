<?php
/**
 * This is the listing page for ProductCodeType
 * 
 * @package    Web
 * @subpackage Controller
 * @author     lhe<helin16@gmail.com>
 */
class QuestionController extends CRUDPageAbstract
{
	/**
	 * (non-PHPdoc)
	 * @see BPCPageAbstract::$menuItem
	 */
	public $menuItem = 'questions';
	protected $_focusEntity = 'Question';
	/**
	 * constructor
	 */
	public function __construct()
	{
		parent::__construct();
		if(!AccessControl::canAccessQuestionListingPage(Core::getRole()))
			die('You do NOT have access to this page');
	}
	/**
	 * (non-PHPdoc)
	 * @see CRUDPageAbstract::_getEndJs()
	 */
	protected function _getEndJs()
	{
		$js = parent::_getEndJs();
		$js .= "pageJs.getResults(true, " . $this->pageSize . ");";
		$js .= "pageJs.loadSelect2();";
		$js .= "pageJs._bindSearchKey();";
		$js .= 'pageJs.setCallbackId("updateItem", "' . $this->updateItemBtn->getUniqueID(). '");';
		return $js;
	}
	/**
	 * Getting the items
	 *
	 * @param unknown $sender
	 * @param unknown $param
	 * @throws Exception
	 *
	 */
	public function getItems($sender, $param)
	{
		$results = $errors = array();
		try
		{
			$class = trim($this->_focusEntity);
			
			if(isset($param->CallbackParameter->id) && ($question = Question::get(intval($param->CallbackParameter->id))) instanceof Question)
				$results['item'] = $question->getJson();
			else
			{
				$pageNo = 1;
				$pageSize = DaoQuery::DEFAUTL_PAGE_SIZE;
				if(isset($param->CallbackParameter->pagination))
				{
					$pageNo = $param->CallbackParameter->pagination->pageNo;
					$pageSize = $param->CallbackParameter->pagination->pageSize;
				}
				
				/////
				
				$title = array('txt' => '', 'token' => false);
				$content = '';
				$authorId = 0;
				$authorName = '';
				$refId = '';
				$vote = '';
				$active = true;
				$created = array('from' => null, 'to' => null);
				$updated = array('from' => null, 'to' => null);
				$topics = array();
				$units = array();
				$pageNo = $pageNo;
				$pageSize = $pageSize;
				$orderBy = array('created' => 'desc');
				$stats = array();
				
				$serachCriteria = isset($param->CallbackParameter->searchCriteria) ? json_decode(json_encode($param->CallbackParameter->searchCriteria), true) : array();
					
				$where = array(1);
				$params = array();
				foreach($serachCriteria as $field => $value)
				{
					if((is_array($value) && count($value) === 0) || (is_string($value) && ($value = trim($value)) === ''))
						continue;
					
					$query = $class::getQuery();
					switch ($field)
					{
						case 'quest.title':
							{
								if($field === 'quest.title' && (!isset($serachCriteria['quest.title.token']) || ($token = (strtolower(trim($serachCriteria['quest.title.token'])) !== 'on'))))
								{
									$title['txt'] = $value;
								} else {
									$title['token'] = true;
									$title['txt'] = $value;
								}
								break;
							}
						case 'quest.content':
							{
								if($field === 'quest.content' && (!isset($serachCriteria['quest.content.token']) || ($token = (strtolower(trim($serachCriteria['quest.content.token'])) !== 'on'))))
								{
									$content['txt'] = $value;
								} else {
									$content['token'] = true;
									$content['txt'] = $value;
								}
								break;
							}
						case 'quest.active':
							{
								if(intval($value) === 3)
									$active = null;
								else $active = (intval($value) === 1);
								break;
							}
						case 'quest.topics':
							{
								if(!is_array($value) && intval($value) !== 0)
									$topics = [intval($value)];
								elseif(is_array($value) && count($value) > 0)
									$topics = $value;
								break;
							}
						case 'quest.units':
							{
								if(!is_array($value) && intval($value) !== 0)
									$units = [intval($value)];
								elseif(is_array($value) && count($value) > 0)
									$units = $value;
								break;
							}
						case 'quest.created_from':
							{
								if(trim($value) !== '' && ($from = date_parse_from_format("d/m/Y", trim($value))) !== false)
								{
									$created['from'] = new UDate();
									$created['from']->setTimeZone(UDate::TIME_ZONE_MELB);
									$created['from']->setDate($from['day'], $from['month'], $from['year']);
									$created['from']->setTime(0, 0, 0);
									$created['from']->setTimeZone();
								}
								break;
							}
						case 'quest.created_to':
							{
								if(trim($value) !== '' && ($to = date_parse_from_format("d/m/Y", trim($value))) !== false)
								{
									$created['to'] = new UDate();
									$created['to']->setTimeZone(UDate::TIME_ZONE_MELB);
									$created['to']->setDate($to['day'], $to['month'], $to['year']);
									$created['to']->setTime(23, 59, 59);
								}
								break;
							}
					}
				}
				var_dump([
					$title, $content, $authorId, $authorName, $refId, $vote, $active, $created, $updated, $topics, $units, $pageNo, $pageSize, $orderBy, $stats
				]);
				$objects = Question::getQuestions(
					$title, $content, $authorId, $authorName, $refId, $vote, $active, $created, $updated, $topics, $units, $pageNo, $pageSize, $orderBy, $stats
				);
				$results['pageStats'] = $stats;
				$results['items'] = array();
				foreach($objects as $obj)
					$results['items'][] = $obj->getJson();
			}
		}
		catch(Exception $ex)
		{
			$errors[] = $ex->getMessage();
		}
		$param->ResponseData = StringUtilsAbstract::getJson($results, $errors);
	}
	/**
	 * delete the items
	 *
	 * @param unknown $sender
	 * @param unknown $param
	 * @throws Exception
	 *
	 */
	public function deleteItems($sender, $param)
	{
		$results = $errors = array();
		try
		{
			$class = trim($this->_focusEntity);
			$ids = isset($param->CallbackParameter->ids) ? $param->CallbackParameter->ids : array();
			$deactivate = isset($param->CallbackParameter->deactivate) ? ($param->CallbackParameter->deactivate===true) : false;
			if(count($ids) > 0)
			{
				if($deactivate === true)
				{
					foreach ($ids as $id)
					{
						$obj = $class::get($id);
						if($obj instanceof $class)
							$obj->setActive(false)->save();
					}
				}
				else $class::deleteByCriteria('id in (' . str_repeat('?', count($ids)) . ')', $ids);
				if($obj instanceof Question)
					QuestionConnector::sync($obj);
			}
		}
		catch(Exception $ex)
		{
			$errors[] = $ex->getMessage();
		}
		$param->ResponseData = StringUtilsAbstract::getJson($results, $errors);
	}
	/**
	 * save the items
	 *
	 * @param unknown $sender
	 * @param unknown $param
	 * @throws Exception
	 *
	 */
	public function saveItem($sender, $param)
	{
		$results = $errors = array();
		try
		{
			$class = trim($this->_focusEntity);
			if(!isset($param->CallbackParameter->item))
				throw new Exception("System Error: no item information passed in!");
			$item = (isset($param->CallbackParameter->item->id) && ($item = $class::get($param->CallbackParameter->item->id)) instanceof $class) ? $item : null;
			$name = trim($param->CallbackParameter->item->name);
			$description = trim($param->CallbackParameter->item->description);
			$allowMultiple = (!isset($param->CallbackParameter->item->allowMultiple) || $param->CallbackParameter->item->allowMultiple !== true ? false : true);
			
			if($item instanceof $class)
			{
				$item->setName($name)
					->setDescription($description)
					->setAllowMultiple($allowMultiple)
					->save();
			}
			else
			{
				$item = $class::create($name, $description);
			}
			$results['item'] = $item->getJson();
		}
		catch(Exception $ex)
		{
			$errors[] = $ex->getMessage();
		}
		$param->ResponseData = StringUtilsAbstract::getJson($results, $errors);
	}
	public function updateItem($sender, $param)
	{
		$results = $errors = array();
		try
		{
			$class = trim($this->_focusEntity);
			if(!isset($param->CallbackParameter->itemId) || ($itemId = intval($param->CallbackParameter->itemId)) === 0 || !($item = $class::get($itemId)) instanceof $class)
				throw new Exception('Invalid itemId passed in');
			if(!isset($param->CallbackParameter->entityId) || ( strtolower(trim($param->CallbackParameter->entityId)) !== 'new' && ($entityId = intval($param->CallbackParameter->entityId)) === 0 ))
				throw new Exception('Invalid entityId passed in');
			if(!isset($param->CallbackParameter->method) || ($method = trim($param->CallbackParameter->method)) === '')
				throw new Exception('Invalid method passed in');
			
			switch ($method)
			{
				case 'removeTopic':
					{
						QuestionInfo::deleteByCriteria('entityName = ? and entityId = ?',array('Topic', $entityId));
						break;
					}
				case 'addTopic':
					{
						if(($obj = Topic::get($entityId)) instanceof Topic)
							$item->addTopic($obj);
						break;
					}
				case 'removeUnit':
					{
						QuestionInfo::deleteByCriteria('entityName = ? and entityId = ?',array('Unit', $entityId));
						break;
					}
				case 'addUnit':
					{
						if(($obj = Unit::get($entityId)) instanceof Unit)
							$item->addUnit($obj);
						break;
					}
			}
			if($item instanceof Question)
				QuestionConnector::sync($item);
			$results['item'] = $item->getJson();
		}
		catch(Exception $ex)
		{
			$errors[] = $ex->getMessage();
		}
		$param->ResponseData = StringUtilsAbstract::getJson($results, $errors);
	}
}
?>
