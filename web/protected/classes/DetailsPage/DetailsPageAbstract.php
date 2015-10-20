<?php
/**
 * The DetailsPage Page Abstract
 *
 * @package    Web
 * @subpackage Class
 * @author     lhe<helin16@gmail.com>
 */
abstract class DetailsPageAbstract extends BPCPageAbstract
{
	/**
	 * The focusing entity
	 *
	 * @var string
	 */
	protected $_focusEntity = null;
	/**
	 * @var TCallback
	 */
	private $_saveItemBtn;
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
	 * @see TPage::onPreInit()
	 */
	public function onPreInit($param)
	{
		parent::onPreInit($param);
		if(isset($_REQUEST['blanklayout']) && trim($_REQUEST['blanklayout']) === '1')
			$this->getPage()->setMasterClass("Application.layout.BlankLayout");
	}
	/**
	 * (non-PHPdoc)
	 * @see TControl::onInit()
	 */
	public function onInit($param)
	{
		parent::onInit($param);

		$this->_saveItemBtn = new TCallback();
		$this->_saveItemBtn->ID = 'saveItemBtn';
		$this->_saveItemBtn->OnCallback = 'Page.saveItem';
		$this->getControls()->add($this->_saveItemBtn);
	}
	/**
	 * Getting The end javascript
	 *
	 * @return string
	 */
	protected function _getEndJs()
	{
		$js = parent::_getEndJs();
		$class = trim($this->_focusEntity);
		if($class === '' || !isset($this->Request['id']) )
			die('System Error: no id or class passed in');
		if(trim($this->Request['id']) === 'new')
			$entity = new $class();
		else if(!($entity = $class::get($this->Request['id'])) instanceof $class)
			die('invalid item!');

		$js .= "pageJs.setHTMLID('itemDiv', 'item-div')";
		$js .= ".setItem(" . (trim($entity->getId()) === '' ? '{}' : json_encode($entity->getJson())) . ")";
		$js .= ".setCallbackId('saveItem', '" . $this->_saveItemBtn->getUniqueID() . "');";
		$js .= "pageJs._focusEntity = '" . $this->getFocusEntity() . "';";
		return $js;
	}
	/**
	 * getting the focus entity
	 *
	 * @return string
	 */
	public function getFocusEntity()
	{
		return trim($this->_focusEntity);
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
			if (!isset ( $params->CallbackParameter->name ) || ($name = trim ( $params->CallbackParameter->name )) === '')
				throw new Exception ( 'System Error: invalid name passed in.' );
			$refId = '';
			if (isset ( $params->CallbackParameter->refId ) )
				$refId = trim($params->CallbackParameter->refId);
			if (isset ( $params->CallbackParameter->id ) && !($entity = $focusEntity::get(intval($params->CallbackParameter->id))) instanceof $focusEntity )
				throw new Exception ( 'System Error: invalid id passed in.' );
			
			Dao::beginTransaction();
			
			if(!isset($entity) || !$entity instanceof $focusEntity)
				$entity = $focusEntity::create($name,$refId);
			else $entity->setName($name)->setRefId($refId);
			
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