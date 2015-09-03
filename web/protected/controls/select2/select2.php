<?php
/**
 * The select2 Loader
 *
 * @package    web
 * @subpackage controls
 * @author     lhe<helin16@gmail.com>
 */
class select2 extends TClientScript
{
	/**
	 * (non-PHPdoc)
	 * @see TControl::onLoad()
	 */
	public function onLoad($param)
	{
		if(!$this->getPage()->IsPostBack || !$this->getPage()->IsCallback)
		{
			$clientScript = $this->getPage()->getClientScript();
			$clientScript->registerHeadScriptFile('select2.js', "//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.min.js");
			$clientScript->registerStyleSheetFile('select2.css', "//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/css/select2.min.css", 'screen');
		}
	}
}