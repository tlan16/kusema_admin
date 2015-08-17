<?php
/** CommentsInfo Entity
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class CommentsInfo extends InfoAbstract
{
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