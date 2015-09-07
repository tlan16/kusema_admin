<?php
include_once dirname(__FILE__) . '/testAbstract.php';
class fakeQna extends testAbstract
{
	const LOOPS= 600;
	public static function run($clearAll = true)
	{
		parent::run();
		try {
			$transStarted = false;
			try {Dao::beginTransaction();} catch(Exception $e) {$transStarted = true;}
			
			if($clearAll === true)
			{
				Question::deleteByCriteria('id != 0');
				QuestionInfo::deleteByCriteria('id != 0');
				Answer::deleteByCriteria('id != 0');
				AnswerInfo::deleteByCriteria('id != 0');
				Comments::deleteByCriteria('id != 0');
				CommentsInfo::deleteByCriteria('id != 0');
			}
			
			for($loop = 0; $loop < self::LOOPS; $loop++)
			{
				echo 'loop: ' . $loop . PHP_EOL;
				$title = self::getRandQuestion();
				$content = self::getRandSentense(10,50);
				$refId = (rand(1,4) === 1 ? '' : md5($title.$content));
				$author = (rand(1,30) === 1 ? null : self::getRandObj('Person'));
				$authorName = (($author !== null && rand(1,3) === 1) ? self::getRandName(): '');
				$active = (rand(1,50) === 1 ? false : true);
				$basetime = new UDate('2015-00-00 00:00:00');
				
				$question = Question::create($title, $content, $refId, $author, $authorName, $active);
				$question->setCreated($basetime->modify('+'.rand(1,365).' day')->setRandTime())->save();
				echo 'Question[' . $question->getId() . '] added' . PHP_EOL;
				$limit = rand(1,5);
				for($i = 0; $i < $limit; $i++) {
					$topic = (rand(1,5) === 1 ? Topic::get(rand(1,7)) : self::getRandObj('Topic'));
					$question->addTopic($topic);
				}
				echo count($question->getTopics()) . ' Topics added to Question[' . $question->getId() . ']' . PHP_EOL;
				$limit = rand(1,5);
				for($i = 0; $i < $limit; $i++) {
					$unit = (rand(1,5) === 1 ? Unit::get(rand(1,7)) : self::getRandObj('Unit'));
					$question->addUnit($unit);
				}
				echo count($question->getUnits()) . ' Units added to Question[' . $question->getId() . ']' . PHP_EOL;
				$limit = rand(1,5);
				for($i = 0; $i < $limit; $i++) {
					$title = self::getRandQuestion();
					$content = self::getRandSentense(5,50);
					$refId = md5($title.$content);
					$author = self::getRandObj('Person');
					$authorName = self::getRandName();;
					$active = true;
					$comment = $question->addComments($title, $content, $refId, $author, $authorName, $active);
					$comment->setCreated($question->getCreated()->modify('+'.rand(1,30).' day')->setRandTime())->save();
					echo 'Comment[' . $comment->getId() . '] added to Question[' . $question->getId() . ']' . PHP_EOL;
					$limit2 = rand(1,3);
					for($j = 0; $j < $limit2; $j++) {
						$title = self::getRandQuestion();
						$content = self::getRandSentense(5,50);
						$refId = md5($title.$content);
						$author = self::getRandObj('Person');
						$authorName = self::getRandName();;
						$active = true;
						$commentOfComment = $comment->addComments($title, $content, $refId, $author, $authorName, $active);
						$commentOfComment->setCreated($comment->getCreated()->modify('+'.rand(1,7).' day')->setRandTime())->save();
						$limit3 = rand(1,2);
						for($k = 0; $k < $limit3; $k++) {
							$title = self::getRandQuestion();
							$content = self::getRandSentense(5,50);
							$refId = md5($title.$content);
							$author = self::getRandObj('Person');
							$authorName = self::getRandName();;
							$active = true;
							$commentOfComment->addComments($title, $content, $refId, $author, $authorName, $active)
											->setCreated($commentOfComment->getCreated()->modify('+'.rand(1,3).' day')->setRandTime())->save();
						}
						echo count($comment->getComments()) . ' Comments added to Comment[' . $comment->getId() . '] which belongs to Question[' . $question->getId() . ']' . PHP_EOL;
					}
				}
				$limit = rand(8,20);
				for($i = 0; $i < $limit; $i++) {
					$title = self::getRandQuestion();
					$content = self::getRandSentense(5,50);
					$refId = md5($title.$content);
					$author = self::getRandObj('Person');
					$authorName = self::getRandName();;
					$active = true;
					$anwser = $question->addAnswer($title, $content, $refId, $author, $authorName, $active);
					$anwser->setCreated($question->getCreated()->modify('+'.rand(1,30).' day')->setRandTime())->save();
					echo 'Answer[' . $anwser->getId() . '] added to Question[' . $question->getId() . ']' . PHP_EOL;
					$limit2 = rand(1,5);
					for($j = 0; $j < rand(1,5); $j++) {
						$title = self::getRandQuestion();
						$content = self::getRandSentense(5,50);
						$refId = md5($title.$content);
						$author = self::getRandObj('Person');
						$authorName = self::getRandName();;
						$active = true;
						$comment = $anwser->addComments($title, $content, $refId, $author, $authorName, $active);
						$comment->setCreated($anwser->getCreated()->modify('+'.rand(1,30).' day')->setRandTime())->save();
						echo 'Comment[' . $comment->getId() . '] added to Anwser[' . $anwser->getId() . ']' . PHP_EOL;
						$limit3 = rand(1,3);
						for($k = 0; $k < $limit3; $k++) {
							$title = self::getRandQuestion();
							$content = self::getRandSentense(5,50);
							$refId = md5($title.$content);
							$author = self::getRandObj('Person');
							$authorName = self::getRandName();;
							$active = true;
							$commentOfComment = $comment->addComments($title, $content, $refId, $author, $authorName, $active);
							$commentOfComment->setCreated($comment->getCreated()->modify('+'.rand(1,7).' day')->setRandTime())->save();
							$comment->setCreated($question->getCreated()->modify('+'.rand(1,30).' day')->setRandTime())->save();
							$limit4 = rand(1,3);
							for($l = 0; $l < $limit4; $l++) {
								$title = self::getRandQuestion();
								$content = self::getRandSentense(5,50);
								$refId = md5($title.$content);
								$author = self::getRandObj('Person');
								$authorName = self::getRandName();;
								$active = true;
								$commentOfComment->addComments($title, $content, $refId, $author, $authorName, $active)
												->setCreated($commentOfComment->getCreated()->modify('+'.rand(1,3).' day')->setRandTime())->save();
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