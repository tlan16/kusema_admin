<?php
class Controller extends BPCPageAbstract
{
	public $menuItem = 'users';
	/**
	 * constructor
	 */
	public function __construct()
	{
		if(!AccessControl::canAccessUserPage(Core::getRole()))
			die(BPCPageAbstract::show404Page('Access Denied', 'You have no access to this page!'));
		parent::__construct();
	}
	/**
	 * Getting The end javascript
	 *
	 * @return string
	 */
	protected function _getEndJs()
	{
		$js = parent::_getEndJs();
		$js .= 'pageJs.setConfigDiv("configDiv");';
		$js .= 'pageJs.setResultDiv("resultDiv");';
		$js .= 'pageJs.setPresetDiv("presetDiv");';
		$js .= 'pageJs.load();';
		return $js;
	}
}
