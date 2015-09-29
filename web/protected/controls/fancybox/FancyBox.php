<?php
/**
 * The SocialBtns Loader
 *
 * @package    web
 * @subpackage controls
 * @author     lhe<helin16@gmail.com>
 */
class FancyBox extends TClientScript
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
			$folder = $this->publishFilePath(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR);
			// Add jQuery library
			// Add mousewheel plugin (this is optional)
			$clientScript->registerHeadScriptFile('jquery.mousewheel', "https://cdnjs.cloudflare.com/ajax/libs/jquery-mousewheel/3.1.13/jquery.mousewheel.min.js");
			// Add fancyBox main JS and CSS files
			$clientScript->registerStyleSheetFile('jquery.fancybox2.css', "https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.min.css", 'screen');
			$clientScript->registerHeadScriptFile('jquery.fancybox2',  "https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.pack.js");
			
			// Add fancyBox Button helper
			$clientScript->registerStyleSheetFile('jquery.fancybox2.btn.css', "https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/helpers/jquery.fancybox-buttons.css", 'screen');
			$clientScript->registerHeadScriptFile('jquery.fancybox2.btn', "https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/helpers/jquery.fancybox-buttons.js");
			// Add fancyBox Thumbnail helper (this is optional)
			$clientScript->registerStyleSheetFile('jquery.fancybox2.thumb.css', "https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/helpers/jquery.fancybox-thumbs.css", 'screen');
			$clientScript->registerHeadScriptFile('jquery.fancybox2.thumb', "https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/helpers/jquery.fancybox-thumbs.js");
			// Add fancyBox Media helper (this is optional) -->
			$clientScript->registerHeadScriptFile('jquery.fancybox2.media', "https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/helpers/jquery.fancybox-media.js");
			
			$clientScript->registerBeginScript('jquery.noConflict', 'jQuery.noConflict();');
		}
	}
}