<?php
/**
 * This is the listing page for manufacturer
 * 
 * @package    Web
 * @subpackage Controller
 * @author     flan<franklan118@gmail.com>
 */
class ListController extends CRUDPageAbstract
{
	/**
	 * (non-PHPdoc)
	 * @see BPCPageAbstract::$menuItem
	 */
	public $menuItem = 'system.messages';
	protected $_focusEntity = 'Message';
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
		// BPC Class _getEndJs()
		$js = 'if(typeof(PageJs) !== "undefined"){';
		$js .= 'var pageJs = new PageJs();';
		$js .= 'pageJs.setHTMLID("main-form", "' . $this->getPage()->getForm()->getClientID() . '"); ';
		$js .= $this->_preGetEndJs();
		$js .= '}';
		
		$js .= "if(pageJs.init) {pageJs.init();}";
		$js .= "pageJs";
		$js .= ";";
		return $js;
	}
}
?>
