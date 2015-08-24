<?php
include_once dirname(__FILE__) . '/testAbstract.php';
class infoTest extends testAbstract
{
	public static function run()
	{
		parent::run();
		$data = array(
			'Topic',	
			'Unit',
			'Group',
			'Vote',
			'Image',
			'Video',
			'Code'
		);
		$reset = true;
		self::addInfoTypes('QuestionInfoType', $data, $reset);
		self::addInfoTypes('AnswerInfoType', $data);
		self::addInfoTypes('CommentsInfoType', $data);
	}
	private static function addInfoTypes($class, $data)
	{
		$active = true;
		echo 'data size: ' . count($data) . PHP_EOL;
		self::echoTotalNumber($class);
		try {
			$transStarted = false;
			$rowCount = 0;
			try {Dao::beginTransaction();} catch(Exception $e) {$transStarted = true;}
			foreach ($data as $name)
			{
				$obj = $class::create($name, $active);
		
				echo 'TESTING ' . $class . PHP_EOL;
				echo 'JSON: ' . PHP_EOL . print_r(self::getRealJson($obj->getJson()), true);
			}
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
		self::echoTotalNumber($class);
	}
}

infoTest::run();