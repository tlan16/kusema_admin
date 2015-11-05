<?php
/**
 * The bootstrapMarkdown Library Loader
 *
 * @package    web
 * @subpackage controls
 * @author     flan<franklan118@gmail.com>
 */
class bootstrapMarkdown extends TClientScript
{
	/**
	 * (non-PHPdoc)
	 * @see TControl::onLoad()
	 */
	public function onLoad($param)
	{
		$clientScript = $this->getPage()->getClientScript();
		if(!$this->getPage()->IsPostBack || !$this->getPage()->IsCallback)
		{
// 			$folder = $this->publishFilePath(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR);
			// Add jQuery library
			// Add mousewheel plugin (this is optional)
			$clientScript->registerScriptFile('markdown.js', "https://cdnjs.cloudflare.com/ajax/libs/markdown.js/0.5.0/markdown.min.js");
			$clientScript->registerScriptFile('bstp_markdown.js', "https://cdnjs.cloudflare.com/ajax/libs/bootstrap-markdown/2.9.0/js/bootstrap-markdown.min.js");
			$clientScript->registerStyleSheetFile('bstp_markdown.css', "https://cdnjs.cloudflare.com/ajax/libs/bootstrap-markdown/2.9.0/css/bootstrap-markdown.min.css", 'screen');
		}
	}
}