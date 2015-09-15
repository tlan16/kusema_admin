<?php
/**
 * The iframeResizer Library Loader
 *
 * @package    web
 * @subpackage controls
 * @author     lhe<helin16@gmail.com>
 */
class iframeResizer extends TClientScript
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
			$folder = $this->publishFilePath(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR);
			$clientScript->registerScriptFile('iframeResizer.js', $folder . '/iframeResizer.js');
			$clientScript->registerScriptFile('iframeResizer.contentWindow.js', $folder . '/iframeResizer.contentWindow.js');
		}
	}
}