<?php
/** CommentsInfo Entity
 *
 * @package    Core
 * @subpackage Entity
 * @author     flan<franklan118@gmail.com>
 */
class CommentsInfo extends InfoAbstract
{
	/**
	 * The question of the QuestionInfo
	 * @var Question
	 */
	protected $question;
	/**
	 * Getter for question
	 *
	 * @return Question
	 */
	public function getQuestion()
	{
		$this->loadManyToOne('question');
		return $this->question;
	}
	/**
	 * Setter for question
	 *
	 * @param Question $value The question
	 *
	 * @return QuestionInfo
	 */
	public function setQuestion($value)
	{
		$this->question = $value;
		return $this;
	}
	
	/**
	 * The question of the QuestionInfo
	 * @var Question
	 */
	protected $answer;
	/**
	 * getter for answer
	 *
	 * @return Anser
	 */
	public function getAnswer()
	{
		$this->loadManyToOne('answer');
		return $this->answer;
	}
	/**
	 * Setter for answer
	 *
	 * @return AnswerInfo
	 */
	public function setAnswer($answer)
	{
		$this->answer = $answer;
		return $this;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see BaseEntity::__loadDaoMap()
	 */
	public function __loadDaoMap()
	{
		DaoMap::begin($this, 'comm_info');
	
		parent::__loadDaoMap();
	
		DaoMap::commit();
	}
}