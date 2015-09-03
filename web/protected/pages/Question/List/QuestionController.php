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
		if(!AccessControl::canAccessDevelopingPage(Core::getRole()))
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
			$pageNo = 1;
			$pageSize = DaoQuery::DEFAUTL_PAGE_SIZE;
			if(isset($param->CallbackParameter->pagination))
			{
				$pageNo = $param->CallbackParameter->pagination->pageNo;
				$pageSize = $param->CallbackParameter->pagination->pageSize;
			}
			
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
					case 'quest.content':
						{
							if($field === 'quest.content' && (!isset($serachCriteria['quest.content.token']) || ($token = (strtolower(trim($serachCriteria['quest.content.token'])) !== 'on'))))
							{
								$where[] =  $field . " like ? ";
								$params[] = '%' . $value . '%';
								break;
							} else {
								$searchTokens = array();
								StringUtilsAbstract::permute(preg_split("/[\s,]+/", $value), $searchTokens);
								$likeArray = array();
								foreach($searchTokens as $index => $tokenArray) {
									$key = 'token' . $index;
									$params[$key] = '%' . implode('%', $tokenArray) . '%';
									$likeArray[] = $field . " like :" . $key;
								}
								
								$where[] = '(' . implode(' OR ', $likeArray) . ')';
								break;
							}
						}
					case 'quest.active':
						{
							$value = intval($value);
							if($value === 0 || $value === 1)
							{
								$where[] =  $field . " = ? ";
								$params[] = $value;
							}
							break;
						}
				}
			}
			$stats = array();
			$objects = $class::getAllByCriteria(implode(' AND ', $where), $params, false, $pageNo, $pageSize, array('id' => 'asc'), $stats);
			$results['pageStats'] = $stats;
			$results['items'] = array();
			foreach($objects as $obj)
				$results['items'][] = $obj->getJson();
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
			var_dump($param->CallbackParameter);
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
						if(QuestionInfo::get($entityId) instanceof QuestionInfo)
							QuestionInfo::deleteByCriteria('id=?',array($entityId));
						break;
					}
				case 'addTopic':
					{
						if(($obj = Topic::get($entityId)) instanceof Topic)
							$item->addTopic($obj);
						break;
					}
			}
			
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
