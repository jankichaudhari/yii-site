<?php

class AdminPagination extends CPagination
{
	public function createPageUrl($controller, $page)
	{

		$params                 = $this->params === null ? $_GET : $this->params;
		$params[$this->pageVar] = $page + 1;
		return $controller->createUrl($this->route, $params);
	}
}
