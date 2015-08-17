<?php
/** AnswerInfoType Entity
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class AnswerInfoType extends InfoEntityAbstract
{
	/**
	 * (non-PHPdoc)
	 * @see BaseEntity::__loadDaoMap()
	 */
	public function __loadDaoMap()
	{
		DaoMap::begin($this, 'ans_info_type');
	
		parent::__loadDaoMap();
	
		DaoMap::commit();
	}
}