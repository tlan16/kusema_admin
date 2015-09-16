<?php
class QuestionConnector extends ForumConnector
{
	public function getList($attributes = array())
	{
		$result = array();
		$url = $this->_rest . 'questions';
		echo $url . PHP_EOL;
		$extraAttributes = array('author','downVotes','upVotes','comments','answers');
		foreach ($extraAttributes as $value)
			self::addExtraAttribute($attributes, 'path', $value);
		$result = $this->getData($url, $attributes);
		return $result;
	}
}