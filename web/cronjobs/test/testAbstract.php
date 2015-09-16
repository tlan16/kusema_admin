<?php
/**
 * Test Abstract
 * 
 * @author Frank-Desktop
 *
 */
abstract class testAbstract
{
	public static function run()
	{
		require_once dirname(__FILE__) . '/../../bootstrap.php';
		Core::setUser(UserAccount::get(UserAccount::ID_SYSTEM_ACCOUNT));
		echo 'START ' . get_called_class() . ' at ' . UDate::now(UDate::TIME_ZONE_MELB) . PHP_EOL;
	}
	public static function getRandQuestion()
	{
		$data = file_get_contents(dirname(__FILE__) . '/questions.list');
		$questions = array();
		foreach (explode(',', $data) as $question) {
			if(($question = trim($question)) !== '')
				$questions[] = $question;
		}
		if(count($questions) === 0)
			return microtime();
		$index = rand(0, count($questions)-1);
		return $questions[$index];
	}
	public static function getRandSentense($min = 5, $max = 10)
	{
		$data = file_get_contents(dirname(__FILE__) . '/sentenses.list');
		$sentenses = array();
		foreach (explode(',', $data) as $sentense) {
			if(($sentense = trim($sentense)) !== '')
				$sentenses[] = $sentense;
		}
		if(count($sentenses) === 0)
			return microtime();
		$result = '';
		for($i = 0; $i < rand($min, $max); $i++)
		{
			$index = rand(0, count($sentenses)-1 );
			$result .= $sentenses[$index] . ' ' . (rand(0,3) === 1 ? "\n" : '');
		}
		return $result;
	}
	public static function getRandName()
	{
		$data = file_get_contents(dirname(__FILE__) . '/surnames.list');
		$names = array();
		foreach (explode(',', $data) as $name) {
			if(($name = trim($name)) !== '')
				$names[] = $name;
		}
		if(count($names) === 0)
			return microtime();
		$index = rand(0, count($names)-1);
		return $names[$index];
	}
	public static function getRealJson($json)
	{
		return json_decode(json_encode($json), true);
	}
	public static function getTimeString()
	{
		$obj = UDate::now(UDate::TIME_ZONE_MELB);
		return $obj->format('s_i_h_d_M');
	}
	public static function echoTotalNumber($class) {
		echo 'there are total of ' . count($class::getAll()) . ' ' . $class . PHP_EOL;
	}
	public static function getRandObj($class) {
		$class = trim($class);
		$obj = new $class;
		if(!$obj instanceof BaseEntityAbstract)
			throw new Exception('Invalid class' . $class . 'passed in');
		return $class::get(rand(1,$class::countByCriteria('active = 1')));;
	}
}