<?php
ini_set('max_execution_time', 3*3600);
class StaticsRunner extends StaticsPageAbstract
{
	private function getParam($type, $entity, $action)
	{
		$param = new stdClass();
		$param->CallbackParameter = new stdClass();
		$param->CallbackParameter->searchCriterias = '';
		$param->CallbackParameter->title = '';
		$param->CallbackParameter->type = $type;
		$param->CallbackParameter->entity = $entity;
		$param->CallbackParameter->action = $action;
	
		return $param;
	}
	public static function runStatics() {
		$class = new self();
		$class->getData('',$class->getParam('pie', 'question', 'topunit'));
		$class->getData('',$class->getParam('pie', 'question', 'toptopic'));
		$class->getData('',$class->getParam('stock', 'question', 'yearly'));
		$class->getData('',$class->getParam('stock', 'question', 'daily'));
		$class->getData('',$class->getParam('stock', 'comments', 'yearly'));
		$class->getData('',$class->getParam('stock', 'comments', 'daily'));
		$class->getData('',$class->getParam('stock', 'answer', 'yearly'));
		$class->getData('',$class->getParam('stock', 'answer', 'daily'));
	}
}