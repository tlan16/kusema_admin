<?php
/** CommentsInfoType Entity
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class CommentsInfoType extends InfoTypeAbstract
{
	const ID_TOPIC = 1;
	const ID_UNIT = 2;
	const ID_GROUP = 3;
	const ID_VOTE = 4;
	const ID_IMAGE = 5;
	const ID_VIDEO = 6;
	const ID_CODE = 7;
	/**
	 * (non-PHPdoc)
	 * @see BaseEntity::__loadDaoMap()
	 */
	public function __loadDaoMap()
	{
		DaoMap::begin($this, 'comm_info_type');
				
		parent::__loadDaoMap();

		DaoMap::commit();
	}
}