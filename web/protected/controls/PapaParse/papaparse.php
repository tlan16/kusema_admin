<?php
/**
 * The select2 Loader
 *
 * @package    web
 * @subpackage controls
 * @author     flan<franklan118@gmail.com>
 */
class papaparse extends TClientScript
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
// 			$folder = $this->publishFilePath(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR);
			// Add jQuery library
			// Add mousewheel plugin (this is optional)
			$clientScript->registerHeadScriptFile('papaparse.js', "https://cdnjs.cloudflare.com/ajax/libs/PapaParse/4.1.2/papaparse.min.js");
		}
	}
}