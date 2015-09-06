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
				$title = self::getRandQuestion();
				$content = self::getRandSentense(5,50);
				$refId = md5($title.$content);
				$author = self::getRandObj('Person');
				$authorName = self::getRandName();;
				$active = true;
				$topic = self::getRandObj('Topic');
				$unit = self::getRandObj('Unit');
				
				$question = Question::create($title, $content, $refId, $author, $authorName, $active);
				echo 'Question[' . $question->getId() . '] added' . PHP_EOL;
				for($i = 0; $i < rand(1,5); $i++) {
					$question->addTopic(self::getRandObj('Topic'));
				}
				echo count($question->getTopics()) . ' Topics added to Question[' . $question->getId() . ']' . PHP_EOL;
				for($i = 0; $i < rand(1,5); $i++) {
					$question->addUnit(self::getRandObj('Unit'));
				}
				echo count($question->getUnits()) . ' Units added to Question[' . $question->getId() . ']' . PHP_EOL;
				for($i = 0; $i < rand(1,5); $i++) {
					$title = self::getRandQuestion();
					$content = self::getRandSentense(5,50);
					$refId = md5($title.$content);
					$author = self::getRandObj('Person');
					$authorName = self::getRandName();;
					$active = true;
					$comment = $question->addComments($title, $content, $refId, $author, $authorName, $active);
					echo 'Comment[' . $comment->getId() . '] added to Question[' . $question->getId() . ']' . PHP_EOL;
					for($i = 0; $i < rand(1,3); $i++) {
						$title = self::getRandQuestion();
						$content = self::getRandSentense(5,50);
						$refId = md5($title.$content);
						$author = self::getRandObj('Person');
						$authorName = self::getRandName();;
						$active = true;
						$commentOfComment = $comment->addComments($title, $content, $refId, $author, $authorName, $active);
						for($i = 0; $i < rand(1,2); $i++) {
							$title = self::getRandQuestion();
							$content = self::getRandSentense(5,50);
							$refId = md5($title.$content);
							$author = self::getRandObj('Person');
							$authorName = self::getRandName();;
							$active = true;
							$commentOfComment->addComments($title, $content, $refId, $author, $authorName, $active);
						}
						echo count($comment->getComments()) . ' Comments added to Comment[' . $comment->getId() . '] which belongs to Question[' . $question->getId() . ']' . PHP_EOL;
					}
				}
				for($i = 0; $i < rand(1,20); $i++) {
					$title = self::getRandQuestion();
					$content = self::getRandSentense(5,50);
					$refId = md5($title.$content);
					$author = self::getRandObj('Person');
					$authorName = self::getRandName();;
					$active = true;
					$anwser = $question->addAnswer($title, $content, $refId, $author, $authorName, $active);
					echo 'Answer[' . $anwser->getId() . '] added to Question[' . $question->getId() . ']' . PHP_EOL;
					for($i = 0; $i < rand(1,5); $i++) {
						$title = self::getRandQuestion();
						$content = self::getRandSentense(5,50);
						$refId = md5($title.$content);
						$author = self::getRandObj('Person');
						$authorName = self::getRandName();;
						$active = true;
						$comment = $anwser->addComments($title, $content, $refId, $author, $authorName, $active);
						echo 'Comment[' . $comment->getId() . '] added to Anwser[' . $anwser->getId() . ']' . PHP_EOL;
						for($i = 0; $i < rand(1,3); $i++) {
							$title = self::getRandQuestion();
							$content = self::getRandSentense(5,50);
							$refId = md5($title.$content);
							$author = self::getRandObj('Person');
							$authorName = self::getRandName();;
							$active = true;
							$commentOfComment = $comment->addComments($title, $content, $refId, $author, $authorName, $active);
							for($i = 0; $i < rand(1,2); $i++) {
								$title = self::getRandQuestion();
								$content = self::getRandSentense(5,50);
								$refId = md5($title.$content);
								$author = self::getRandObj('Person');
								$authorName = self::getRandName();;
								$active = true;
								$commentOfComment->addComments($title, $content, $refId, $author, $authorName, $active);
							}
							echo count($commentOfComment->getComments()) . ' CommentOfComment added to Comment[' . $commentOfComment->getId() . '] which belongs to Answer[' . $anwser->getId() . ']' . PHP_EOL;
						}
						echo count($comment->getComments()) . ' Comments added to Answer[' . $anwser->getId() . ']' . PHP_EOL;
					}
				}
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

$time_start = microtime(true);
fakeQna::run();
echo PHP_EOL . 'Execution time for ' . fakeQna::LOOPS . ' loops: ' . date("H:i:s",microtime(true)-$time_start) . PHP_EOL;