<?php
/** QuestionInfo Entity
 *
 * @package    Core
 * @subpackage Entity
 * @author     lhe<helin16@gmail.com>
 */
class QuestionInfo extends InfoAbstract
{
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