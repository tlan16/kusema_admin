<?php
/**
 * Ajax Controller
 *
 * @package	web
 * @subpackage	Controller-Page
 *
 * @version	1.0
 *
 * @todo :NOTE If anyone copies this controller, then you require this method to profile ajax requests
 */
class AjaxController extends TService
{
  	/**
  	 * Run
  	 */
  	public function run()
  	{
//   		if(!($this->getUser()->getUserAccount() instanceof UserAccount))
//   			die (BPCPageAbstract::show404Page('Invalid request',"No defined access."));

  		$results = $errors = array();
		try
		{
  			$method = '_' . ((isset($this->Request['method']) && trim($this->Request['method']) !== '') ? trim($this->Request['method']) : '');
            if(!method_exists($this, $method))
                throw new Exception('No such a method: ' . $method . '!');
			$results = $this->$method($_REQUEST);
		}
		catch (Exception $ex)
		{
			$errors[] = $ex->getMessage();
		}
		$this->getResponse()->flush();
        $this->getResponse()->appendHeader('Content-Type: application/json');
        $this->getResponse()->write(StringUtilsAbstract::getJson($results, $errors));
  	}
  	private function _getUnit(Array $params)
  	{
  		if(!isset($params['searchTxt']) || ($searchTxt = trim($params['searchTxt'])) === '')
  			throw new Exception('SYSTEM ERROR: INCOMPLETE searchTxt PROVIDED');
  	
  		$pageSize = (isset($params['pageSize']) && ($pageSize = trim($params['pageSize'])) !== '' ? $pageSize : DaoQuery::DEFAUTL_PAGE_SIZE);
  		$pageNo = (isset($params['pageNo']) && ($pageNo = trim($params['pageNo'])) !== '' ? $pageNo : 1);
  		$orderBy = (isset($params['orderBy']) ? $params['orderBy'] : array('updated' => 'desc'));
  	
		$where = ' name like :searchTxt or code like :searchTxt';
  		$sqlParams = array('searchTxt' => '%' . $searchTxt . '%');
  		$returnArray = json_encode(array());
  		$stats = array();
  		$unitArray = Unit::getAllByCriteria($where, $sqlParams);
  		$results = array();
  		$results['items'] = array_map(create_function('$a', 'return $a->getJson();'), $unitArray);
  		$results['pageStats'] = $stats;
  		return $results;
  	}
	/**
	 * Getting the comments for an entity
	 *
	 * @param array $params
	 *
	 * @return string The json string
	 */
  	private function _getAnswers(Array $params)
  	{
  		if(!isset($params['entityId']) || !isset($params['entity']) || ($entityId = trim($params['entityId'])) === '' || ($entity = trim($params['entity'])) === '')
  			throw  new Exception('SYSTEM ERROR: INCOMPLETE DATA PROVIDED');

  		$pageSize = (isset($params['pageSize']) && ($pageSize = trim($params['pageSize'])) !== '' ? $pageSize : DaoQuery::DEFAUTL_PAGE_SIZE);
  		$pageNo = (isset($params['pageNo']) && ($pageNo = trim($params['pageNo'])) !== '' ? $pageNo : null);
  		$orderBy = (isset($params['orderBy']) ? $params['orderBy'] : array('updated' => 'desc'));

  		$where ='entityName = ? and entityId = ?';
  		$sqlParams = array($entity, $entityId);
  		if(isset($params['authorId']) && ($authorId = trim($params['authorId'])) !== '')
  		{
  			$where .= 'and authorId = ?';
  			$sqlParams[] = trim($authorId);
  		}
  		$returnArray = json_encode(array());
  		$stats = array();
  		$commentsArray = Answer::getAllByCriteria($where, $sqlParams, true, $pageNo, $pageSize, $orderBy, $stats);
  		$results = array();
  		$results['items'] = array_map(create_function('$a', 'return $a->getJson();'), $commentsArray);
  		$results['pageStats'] = $stats;
  		return $results;
  	}
	/**
	 * Getting the comments for an entity
	 *
	 * @param array $params
	 *
	 * @return string The json string
	 */
  	private function _getComments(Array $params)
  	{
  		if(!isset($params['entityId']) || !isset($params['entity']) || ($entityId = trim($params['entityId'])) === '' || ($entity = trim($params['entity'])) === '')
  			throw  new Exception('SYSTEM ERROR: INCOMPLETE DATA PROVIDED');

  		$pageSize = (isset($params['pageSize']) && ($pageSize = trim($params['pageSize'])) !== '' ? $pageSize : DaoQuery::DEFAUTL_PAGE_SIZE);
  		$pageNo = (isset($params['pageNo']) && ($pageNo = trim($params['pageNo'])) !== '' ? $pageNo : 1);
  		$orderBy = (isset($params['orderBy']) ? $params['orderBy'] : array('updated' => 'desc'));

  		$where ='entityName = ? and entityId = ?';
  		$sqlParams = array($entity, $entityId);
  		if(isset($params['type']) && ($commentType = trim($params['type'])) !== '')
  		{
  			$where .= 'and type = ?';
  			$sqlParams[] = trim($commentType);
  		}
  		$returnArray = json_encode(array());
  		$stats = array();
  		$commentsArray = Comments::getAllByCriteria($where, $sqlParams, true, $pageNo, $pageSize, $orderBy, $stats);
  		$results = array();
  		$results['items'] = array_map(create_function('$a', 'return $a->getJson();'), $commentsArray);
  		$results['pageStats'] = $stats;
  		return $results;
  	}
  	/**
  	 * Getting an entity
  	 *
  	 * @param unknown $params
  	 *
  	 * @throws Exception
  	 * @return multitype:
  	 */
  	private function _get($params)
  	{
  		if(!isset($params['entityName']) || ($entityName = trim($params['entityName'])) === '')
  			throw new Exception('What are we going to get?');
  		if(!isset($params['entityId']) || ($entityId = trim($params['entityId'])) === '')
  			throw new Exception('What are we going to get with?');
  		return ($entity = $entityName::get($entityId)) instanceof BaseEntityAbstract ? $entity->getJson() : array();
  	}
  	/**
  	 * Getting All for entity
  	 *
  	 * @param unknown $params
  	 *
  	 * @throws Exception
  	 * @return multitype:multitype:
  	 */
  	private function _getAll($params)
  	{
  		if(!isset($params['entityName']) || ($entityName = trim($params['entityName'])) === '')
  			throw new Exception('What are we going to get? (invalid entityName provided)');
  		$searchTxt = trim(isset($params['searchTxt']) ? trim($params['searchTxt']) : '');
  		$searchParams = isset($params['searchParams']) ? $params['searchParams'] : array();
  		$pageNo = isset($params['pageNo']) ? trim($params['pageNo']) : null;
  		$pageSize = isset($params['pageSize']) ? trim($params['pageSize']) : DaoQuery::DEFAUTL_PAGE_SIZE;
  		$active = isset($params['active']) ? intval($params['active']) : 1;
  		$orderBy = (isset($params['orderBy']) ? trim($params['orderBy']) : array('updated' => 'desc'));

  		$stats = array();
  		$items = $entityName::getAllByCriteria($searchTxt, $searchParams, $active, $pageNo, $pageSize, $orderBy, $stats);
  		return array('items' => array_map(create_function('$a', 'return $a->getJson();'), $items), 'pagination' => $stats);
  	}
}
?>