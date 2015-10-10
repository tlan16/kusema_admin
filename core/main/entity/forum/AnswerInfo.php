<?php
/** AnswerInfo Entity
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class AnswerInfo extends InfoAbstract
{
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
		DaoMap::begin($this, 'ans_info');
	
		parent::__loadDaoMap();
	
		DaoMap::commit();
	}
}