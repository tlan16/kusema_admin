<?php
/**
 * This is the StaticsController
 * 
 * @package    Web
 * @subpackage Controller
 * @author     lhe<helin16@gmail.com>
 */
class StaticsController extends StaticsPageAbstract
{
	/**
	 * (non-PHPdoc)
	 * @see BPCPageAbstract::$menuItem
	 */
	public $menuItem = 'statics.order.mthlyTrend';
	/**
	 * Getting The end javascript
	 *
	 * @return string
	 */
	protected function _getEndJs()
	{
		$js = parent::_getEndJs();
		$js .= 'pageJs';
			$js .= '.load("","pie","Top Units");';
		return $js;
	}
	/**
	 * (non-PHPdoc)
	 * @see StaticsPageAbstract::getData()
	 */
	public function getData($sender, $param)
	{
		$results = $errors = array();
		try
		{
			$results = array();
			foreach(Question::getTopUnits() as $unit)
				$results[] = array('id' => $unit['UnitId'], 'name' => $unit['UnitCode'].': '.$unit['UnitName'], 'y' => doubleval($unit['percentage']));
		}
		catch(Exception $ex)
		{
			$errors[] = $ex->getMessage();
		}
		$param->ResponseData = StringUtilsAbstract::getJson($results, $errors);
	}
}
?>
