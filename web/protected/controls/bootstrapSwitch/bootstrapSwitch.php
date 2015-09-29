<?php
/**
 * The select2 Loader
 *
 * @package    web
 * @subpackage controls
 * @author     lhe<helin16@gmail.com>
 */
class bootstrapSwitch extends TClientScript
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
// 			$folder = $this->publishFilePath(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'src/dist' . DIRECTORY_SEPARATOR);
			// Add jQuery library
			// Add mousewheel plugin (this is optional)
			$clientScript->registerHeadScriptFile('bootstrapSwitch.js', "https://cdnjs.cloudflare.com/ajax/libs/bootstrap-switch/3.3.2/js/bootstrap-switch.min.js");
			$clientScript->registerStyleSheetFile('bootstrapSwitch.css', "https://cdnjs.cloudflare.com/ajax/libs/bootstrap-switch/3.3.2/css/bootstrap3/bootstrap-switch.min.css", 'screen');
		}
	}
}