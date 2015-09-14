<?php
ini_set('max_execution_time', '1800'); 
set_time_limit(1800);
/**
 * The StaticsPage Page Abstract
 *
 * @package    Web
 * @subpackage Class
 * @author     lhe<helin16@gmail.com>
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
		$title = (isset($this->Request['title']) ? trim($this->Request['title']) : ucwords(strtolower($type . ' chart for ' . $entity)) );
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
			switch (strtolower(trim($param->CallbackParameter->action)))
			{
				case 'topunit':
				case 'topunits':
				{
					if(strtolower(trim($param->CallbackParameter->entity)) !== 'question')
						throw new Exception('you can only view top units of question');
					if(strtolower(trim($param->CallbackParameter->type)) !== 'pie')
						throw new Exception('you can only view pie chart for top units of question');
					foreach(Question::getTopUnits() as $unit)
						$results[] = array('id' => $unit['UnitId'], 'name' => $unit['UnitCode'].': '.$unit['UnitName'], 'y' => doubleval($unit['percentage']));
					break;
				}
				case 'toptopic':
				case 'toptopics':
				{
					if(strtolower(trim($param->CallbackParameter->entity)) !== 'question')
						throw new Exception('you can only view top topics of question');
					if(strtolower(trim($param->CallbackParameter->type)) !== 'pie')
						throw new Exception('you can only view pie chart for top topics of question');
					foreach(Question::getTopTopics() as $topic)
						$results[] = array('id' => $topic['TopicId'], 'name' => $topic['TopicName'], 'y' => doubleval($topic['percentage']));
					break;
				}
				case 'yearly':
				{
					if(strtolower(trim($param->CallbackParameter->type)) !== 'stock')
						throw new Exception('you can only view stock chart for top topics of question');
					$results = $class::getCreatedCounts(UDate::now()->modify("-1 year"), UDate::now());
					break;
				}
				case 'daily':
				{
					if(strtolower(trim($param->CallbackParameter->type)) !== 'stock')
						throw new Exception('you can only view stock chart for top topics of question');
					$results = $class::getDailyCreatedCounts(UDate::now()->modify("-1 year"), UDate::now());
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
}
?>