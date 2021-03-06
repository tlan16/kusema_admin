<?php
include_once dirname(__FILE__) . '/testAbstract.php';
class StaticsTest extends testAbstract
{
	public static function run()
	{
		parent::run();
		try {
			$transStarted = false;
			try {Dao::beginTransaction();} catch(Exception $e) {$transStarted = true;}
			
			$topics = Question::getTopTopics(1, 10, true, true);
			echo 'Top Topics: ' . PHP_EOL . print_r(json_decode(json_encode($topics)), true);
			
			$units = Question::getTopUnits(1, 10, true, true);
			echo 'Top Units: ' . PHP_EOL . print_r(json_decode(json_encode($units)), true);
			
			if($transStarted === false)
				Dao::commitTransaction();
			else {echo "transStarted === true, nothing is commited! \n";}
		}
		catch(Exception $ex)
		{
			if($transStarted === false)
				Dao::rollbackTransaction();
			throw $ex;
		}
	}
}

$time_start = microtime(true);
StaticsTest::run();
echo PHP_EOL . 'Execution time: ' . date("H:i:s",microtime(true)-$time_start) . PHP_EOL;