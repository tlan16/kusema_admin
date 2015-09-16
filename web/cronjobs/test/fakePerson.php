<?php
include_once dirname(__FILE__) . '/testAbstract.php';
class fakePerson extends testAbstract
{
	const LOOPS= 6000;
	public static function run()
	{
		parent::run();
		try {
			$transStarted = false;
			try {Dao::beginTransaction();} catch(Exception $e) {$transStarted = true;}
			
			for($loop = 0; $loop < self::LOOPS; $loop++)
			{
				echo 'loop: ' . $loop . PHP_EOL;
				$class = 'Person';
				$firstName = self::getRandName();
				$lastName = self::getRandName();
				$domainExt = array("com", "net", "gov", "org", "edu", "biz", "info");
				$index = rand(0, count($domainExt)-1);
				$email = $firstName . '_' . $lastName . '@' . strtolower(self::getRandName()) . '.' . $domainExt[$index];
				$active = true;
				
				$obj = $class::create($firstName, $lastName, $email);
				
				echo $class . '[' . $obj->getId() . '] added' . PHP_EOL;
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
	}
}

fakePerson::run();