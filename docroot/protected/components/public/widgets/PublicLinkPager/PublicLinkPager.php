<?php
class PublicLinkPager extends CLinkPager
{

	public $pageParams = array();

	protected function createPageButton($label, $page, $class, $hidden, $selected)
	{

		if ($hidden || $selected) {
			$class .= ' ' . ($hidden ? self::CSS_HIDDEN_PAGE : self::CSS_SELECTED_PAGE);
		}
		return '<li class="' . $class . '">' . CHtml::link($label, $this->createPageUrl($page)) . '</li>';
	}

	protected function createPageUrl($page)
	{

		$pagination = $this->getPages();

		$this->pageParams[$pagination->pageVar] = $page+1;
		$t                                      = http_build_query($this->pageParams);
		return '?' . $t;
	}

}