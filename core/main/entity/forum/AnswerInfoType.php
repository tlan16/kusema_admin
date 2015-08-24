<?php
/** AnswerInfo Entity
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class AnswerInfoType extends InfoTypeAbstract
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