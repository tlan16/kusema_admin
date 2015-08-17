<?php
/** Question Entity
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class Question extends BaseEntityAbstract
{
	/**
	 * add Comments
	 *
	 * @param string             $title    The title
	 * @param string             $content  The comemnts
	 * @param UserAccount        $author   The author of the comments
	 * @param string             $groupId  The groupId
	 */
	public static function addComments($title, $content, UserAccount $author = null)
	{
		$className = __CLASS__;
		$en = new $className();
		return $en-->setTitle($title)
			->setContent($content)
			->setAuthor($author)
			->save();
	}
	/**
	 * (non-PHPdoc)
	 * @see BaseEntity::__loadDaoMap()
	 */
	public function __loadDaoMap()
	{
		DaoMap::begin($this, 'quest');
				
		parent::__loadDaoMap();
		

		DaoMap::commit();
	}
}