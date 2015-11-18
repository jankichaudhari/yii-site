<?php
interface Filterable
{
	public function setFilterCriteria(CDbCriteria $criteria);
	public function getFilterCriteria();
}
