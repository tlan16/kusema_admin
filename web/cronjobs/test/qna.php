<?php
include_once dirname(__FILE__) . '/testAbstract.php';
class qnaTest extends testAbstract
{
	public static function run()
	{
		parent::run();
		
		$class = 'Question';
		$title = $class . '_title_' . self::getTimeString();
		$content = $class . '_content_' . self::getTimeString();
		$refId = $class . '_ref_' . self::getTimeString();
		$author = UserAccount::get(24);
		$authorName = 'alias of ' . $author->getPerson()->getFullName();
		$active = true;
		
		$obj = $class::create($title, $content, $refId, $author, $authorName, $active);
		
		echo 'TESTING ' . $class . PHP_EOL;
		echo 'JSON: ' . PHP_EOL . print_r(self::getRealJson($obj->getJson()), true);
		
		$class = 'Answer';
		$title = $class . '_title_' . self::getTimeString();
		$content = $class . '_content_' . self::getTimeString();
		$refId = $class . '_ref_' . self::getTimeString();
		$author = UserAccount::get(24);
		$authorName = 'alias of ' . $author->getPerson()->getFullName();
		$active = true;
		
		$obj = $obj->addAnswer($title, $content, $refId, $author, $authorName, $active);
		
		echo 'TESTING ' . $class . PHP_EOL;
		echo 'JSON: ' . PHP_EOL . print_r(self::getRealJson($obj->getJson()), true);
		
		$class = 'Comments';
		$title = $class . '_title_' . self::getTimeString();
		$content = $class . '_content_' . self::getTimeString();
		$refId = $class . '_ref_' . self::getTimeString();
		$author = UserAccount::get(24);
		$authorName = 'alias of ' . $author->getPerson()->getFullName();
		$active = true;
		
		$obj = $obj->addComments($title, $content, $refId, $author, $authorName, $active);
		
		echo 'TESTING ' . $class . PHP_EOL;
		echo 'JSON: ' . PHP_EOL . print_r(self::getRealJson($obj->getJson()), true);
	}
}

qnaTest::run();