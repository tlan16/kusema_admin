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