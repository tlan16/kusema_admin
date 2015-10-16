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
			$clientScript->registerHeadScriptFile('select2.js', "https://cdnjs.cloudflare.com/ajax/libs/select2/3.5.2/select2.min.js");
			$clientScript->registerStyleSheetFile('select2.css', "https://cdnjs.cloudflare.com/ajax/libs/select2/3.5.2/select2.min.css", 'screen');
			$clientScript->registerStyleSheetFile('select2-bootstrap.css', "https://cdnjs.cloudflare.com/ajax/libs/select2/3.5.2/select2-bootstrap.min.css", 'screen');
		}
	}
}