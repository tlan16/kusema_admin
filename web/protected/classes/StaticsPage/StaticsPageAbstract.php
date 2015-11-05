<?php
ini_set('max_execution_time', '1800'); 
set_time_limit(1800);
/**
 * The StaticsPage Page Abstract
 *
 * @package    Web
 * @subpackage Class
 * @author     flan<franklan118@gmail.com>
 */
abstract class StaticsPageAbstract extends BPCPageAbstract
{
	/**
	 * @var TCallback
	 */
	private $_getDataBtn;
	/**
	 * loading the page js class files
	 */
	protected function _loadPageJsClass()
	{
		parent::_loadPageJsClass();
		$thisClass = __CLASS__;
		$cScripts = self::getLastestJS(__CLASS__);
		if (isset($cScripts['js']) && ($lastestJs = trim($cScripts['js'])) !== '')
			$this->getPage()->getClientScript()->registerScriptFile($thisClass . 'Js', $this->publishFilePath(dirname(__FILE__) . DIRECTORY_SEPARATOR . $lastestJs));
		if (isset($cScripts['css']) && ($lastestCss = trim($cScripts['css'])) !== '')
			$this->getPage()->getClientScript()->registerStyleSheetFile($thisClass . 'Css', $this->publishFilePath(dirname(__FILE__) . DIRECTORY_SEPARATOR . $lastestCss));
	    return $this;
	}
	/**
	 * (non-PHPdoc)
	 * @see TControl::onInit()
	 */
	public function onInit($param)
	{
		parent::onInit($param);

		$this->_getDataBtn = new TCallback();
		$this->_getDataBtn->ID = 'getDataBtn';
		$this->_getDataBtn->OnCallback = 'Page.getData';
		$this->getControls()->add($this->_getDataBtn);
	}
	/**
	 * Getting The end javascript
	 *
	 * @return string
	 */
	protected function _getEndJs()
	{
		if(!isset($this->Request['type']) || ($type = trim($this->Request['type'])) === '')
			die('Invalid or No Type passed in');
		if(!isset($this->Request['entity']) || ($entity = trim($this->Request['entity'])) === '' || !class_exists($class = ucfirst(strtolower($entity))) )
			die('Invalid or No Entity passed in');
		$title = (isset($this->Request['title']) && trim($this->Request['title']) !== '' ? trim($this->Request['title']) : ucwords(strtolower($type . ' chart for ' . $entity)) );
		$action = (isset($this->Request['action']) ? trim($this->Request['action']) : null );
		$title .= (trim($action) === '' ? '' : (' - ' . ucfirst(trim($action))) );
		$js = parent::_getEndJs();
		$js .= "pageJs.setCallbackId('getData', '" . $this->_getDataBtn->getUniqueID() . "');";
		$js .= "pageJs.setHTMLID('resultDivId', 'statics-div');";
		$js .= 'pageJs.load("","' . trim($type) . '","' . trim($entity) . '","' . trim($title) . '","' . trim($action) . '");';
		return $js;
	}
	/**
	 * getData
	 *
	 * @param unknown $sender
	 * @param unknown $param
	 * @throws Exception
	 *
	 */
	public function getData($sender, $param)
	{
		if(!isset($param->CallbackParameter->searchCriterias))
			throw new Exception('invalid searchCriterias passed in');
		if(!isset($param->CallbackParameter->title))
			throw new Exception('invalid title passed in');
		if(!isset($param->CallbackParameter->type))
			throw new Exception('invalid type passed in');
		if(!isset($param->CallbackParameter->entity) || !class_exists($class = ucfirst(strtolower(trim($param->CallbackParameter->entity)))) || !($obj = new $class) instanceof BaseEntityAbstract)
			throw new Exception('invalid entity passed in');
		unset($obj);
		if(!isset($param->CallbackParameter->action))
			throw new Exception('invalid action passed in');
		
		$results = $errors = array();
		try
		{
			$results = array();
			switch ($action = strtolower(trim($param->CallbackParameter->action)))
			{
				case 'topunit':
				case 'topunits':
				{
					if(($entity = strtolower(trim($param->CallbackParameter->entity))) !== 'question')
						throw new Exception('entity must be "question" in order to view top units');
					if(($type = strtolower(trim($param->CallbackParameter->type))) !== 'pie')
						throw new Exception('type must be "pie" in order to view top units');
					$statics = Statics::getStatics($entity, $type, $action, true, 0);
					if($statics instanceof Statics)
						$results = json_decode($statics->getData());
					else {
						$obj = Statics::create($entity, $type, $action);
						foreach(Question::getTopUnits() as $unit)
							$results[] = array('id' => $unit['UnitId'], 'name' => $unit['UnitCode'].': '.$unit['UnitName'], 'y' => doubleval($unit['percentage']));
						$obj->setData(json_encode($results))->setStatus(0)->save();
					}
					break;
				}
				case 'toptopic':
				case 'toptopics':
				{
					if(($entity = strtolower(trim($param->CallbackParameter->entity))) !== 'question')
						throw new Exception('entity must be "question" in order to view top topics');
					if(($type = strtolower(trim($param->CallbackParameter->type))) !== 'pie')
						throw new Exception('type must be "pie" in order to view top topics');
					$statics = Statics::getStatics($entity, $type, $action, true, 0);
					if($statics instanceof Statics)
						$results = json_decode($statics->getData());
					else {
						$obj = Statics::create($entity, $type, $action);
						foreach(Question::getTopTopics() as $topic)
							$results[] = array('id' => $topic['TopicId'], 'name' => $topic['TopicName'], 'y' => doubleval($topic['percentage']));
						$obj->setData(json_encode($results))->setStatus(0)->save();
					}
					break;
				}
				case 'yearly':
				{
					if(($entity = strtolower(trim($param->CallbackParameter->entity))) === '')
						throw new Exception('entity must not be empty in order to view 1 year trend');
					if(($type = strtolower(trim($param->CallbackParameter->type))) !== 'stock')
						throw new Exception('type must be "stock" in order to view 1 year trend');
					$statics = Statics::getStatics($entity, $type, $action, true, 0);
					if($statics instanceof Statics)
						$results = json_decode($statics->getData());
					else {
						$obj = Statics::create($entity, $type, $action);
						$results = $class::getCreatedCounts(UDate::now()->modify("-1 year"), UDate::now());
						$obj->setData(json_encode($results))->setStatus(0)->save();
					}
					break;
				}
				case 'daily':
				{
					if(($entity = strtolower(trim($param->CallbackParameter->entity))) === '')
						throw new Exception('entity must not be empty in order to view 1 day trend');
					if(($type = strtolower(trim($param->CallbackParameter->type))) !== 'stock')
						throw new Exception('type must be "stock" in order to view 1 day trend');
					$statics = Statics::getStatics($entity, $type, $action, true, 0);
					if($statics instanceof Statics)
						$results = json_decode($statics->getData());
					else {
						$obj = Statics::create($entity, $type, $action);
						$results = $class::getDailyCreatedCounts(UDate::now()->modify("-1 year"), UDate::now());
						$obj->setData(json_encode($results))->setStatus(0)->save();
					}
					break;
				}
				default:
				{
					throw new Exception('invalid action "' . strtolower(trim($param->CallbackParameter->action)) . '" passed in');
					break;
				}
			}
		}
		catch(Exception $ex)
		{
			$errors[] = $ex->getMessage();
		}
		$param->ResponseData = StringUtilsAbstract::getJson($results, $errors);
	}
	private static function createDummyStatics($entity, $type, $action)
	{
		try {
			Dao::beginTransaction();
			Dao::commitTransaction();
		} catch (Exception $e) {
			Dao::rollbackTransaction();
			throw $e;
		}
	}
}
?>