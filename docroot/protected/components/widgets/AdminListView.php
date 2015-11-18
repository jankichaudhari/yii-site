<?php
Yii::import("zii.widgets.CListView");
class AdminListView extends CListView
{
	/** @var array */
	public $actions = array();

	/** @var string */
	public $title = '';

	public $template = '{summary} {pager} {items} {summary} {pager}';

	public function renderActions()
	{

		if ($this->actions) {

			ob_start();
			foreach ($this->actions as $key => $action) {
				$params = array();
				if (is_array($action)) {
					$params = $action;
					$action = $key;
				}
				$methodName = 'render' . $action . 'Action';
				if (method_exists($this, $methodName)) {
					call_user_func_array(array($this, $methodName), $params);
				} else {
					echo $action;
				}
			}
			$actions = ob_get_clean();

			echo '<div class="block-buttons">' . $actions . '</div>';
		}
	}

	public function run()
	{

		echo '<fieldset>';
		$this->renderTitle();
		$this->renderActions();
		parent::run();
		echo '</fieldset>';
	}

	/**
	 * @param $link
	 */
	public function renderAddAction($link, $title = 'Add new item')
	{

		echo CHtml::link($title, $link, ['class' => 'btn btn-green']);
	}

	public function renderTitle()
	{

		echo '<div class="block-header">' . $this->title . '</div>';
	}

	public function __construct($owner = null)
	{

		parent::__construct($owner);
	}

	public function init()
	{

		if (!$this->cssFile) {
			$this->cssFile = Yii::app()->getBaseUrl(true) . "/css/list-view/style.css";
		}
		if (!isset($this->pager['cssFile']) || !$this->pager['cssFile']) {
			$this->pager['cssFile'] = Yii::app()->getBaseUrl(true) . "/css/pager.css";
		}
		parent::init();
	}

}
