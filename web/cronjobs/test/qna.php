<?php
include_once dirname(__FILE__) . '/testAbstract.php';
class qnaTest extends testAbstract
{
	public static function run()
	{
		parent::run();
		try {
			$transStarted = false;
			try {Dao::beginTransaction();} catch(Exception $e) {$transStarted = true;}
			
			$class = 'Question';
			$title = $class . '_title_' . self::getTimeString();
			$content = $class . '_content_' . self::getTimeString();
			$refId = $class . '_ref_' . self::getTimeString();
			$author = UserAccount::get(24);
			$authorName = 'alias of ' . $author->getPerson()->getFullName();
			$active = true;
			
// 			$obj = $class::create($title, $content, $refId, $author, $authorName, $active);
			$obj = $class::get(3);
// 			$obj->voteUp($author->getPerson());
			$objs = $obj->getVotes();
			echo 'QuestionInfo => ' . PHP_EOL;
			array_map(create_function('$a', 'print_r($a->getJson());'), $objs);
			
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

qnaTest::run();