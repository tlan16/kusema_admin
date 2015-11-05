<?php
/** QuestionInfo Entity
 *
 * @package    Core
 * @subpackage Entity
 * @author     flan<franklan118@gmail.com>
 */
class QuestionInfo extends InfoAbstract
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
	 * (non-PHPdoc)
	 * @see BaseEntity::__loadDaoMap()
	 */
	public function __loadDaoMap()
	{
		DaoMap::begin($this, 'quest_info');
	
		parent::__loadDaoMap();
	
		DaoMap::commit();
	}
}