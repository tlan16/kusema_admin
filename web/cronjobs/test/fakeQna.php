<?php
include_once dirname(__FILE__) . '/testAbstract.php';
class fakeQna extends testAbstract
{
	const LOOPS= 60000;
	public static function run()
	{
		parent::run();
		try {
			$transStarted = false;
			try {Dao::beginTransaction();} catch(Exception $e) {$transStarted = true;}
			
			for($loop = 0; $loop < self::LOOPS; $loop++)
			{
				echo 'loop: ' . $loop . PHP_EOL;
				$class = 'Question';
				$title = self::getRandQuestion();
				$content = self::getRandSentense(5,50);
				$refId = md5($title.$content);
				$author = self::getRandObj('Person');
				$authorName = self::getRandName();;
				$active = true;
				$topic = self::getRandObj('Topic');
				$unit = self::getRandObj('Unit');
				
				$obj = $class::create($title, $content, $refId, $author, $authorName, $active);
				for($i = 0; $i < rand(1,5); $i++) {
					$obj->addTopic(self::getRandObj('Topic'));
				}
				
				echo $class . '[' . $obj->getId() . '] added, with ' . count($obj->getTopics()) . ' Topics' . PHP_EOL;
				
	// 			echo 'TESTING ' . $class . PHP_EOL;
	// 			echo 'JSON: ' . PHP_EOL . print_r(self::getRealJson($obj->getJson()), true);
				
	// 			$class = 'Answer';
	// 			$title = $class . '_title_' . self::getTimeString();
	// 			$content = $class . '_content_' . self::getTimeString();
	// 			$refId = $class . '_ref_' . self::getTimeString();
	// 			$author = UserAccount::get(24);
	// 			$authorName = 'alias of ' . $author->getPerson()->getFullName();
	// 			$active = true;
				
	// 			$obj = $obj->addAnswer($title, $content, $refId, $author, $authorName, $active);
				
	// 			echo 'TESTING ' . $class . PHP_EOL;
	// 			echo 'JSON: ' . PHP_EOL . print_r(self::getRealJson($obj->getJson()), true);
				
	// 			$class = 'Comments';
	// 			$title = $class . '_title_' . self::getTimeString();
	// 			$content = $class . '_content_' . self::getTimeString();
	// 			$refId = $class . '_ref_' . self::getTimeString();
	// 			$author = UserAccount::get(24);
	// 			$authorName = 'alias of ' . $author->getPerson()->getFullName();
	// 			$active = true;
				
	// 			$obj = $obj->addComments($title, $content, $refId, $author, $authorName, $active);
				
	// 			echo 'TESTING ' . $class . PHP_EOL;
	// 			echo 'JSON: ' . PHP_EOL . print_r(self::getRealJson($obj->getJson()), true);
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

fakeQna::run();