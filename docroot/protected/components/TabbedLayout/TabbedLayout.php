<?php
/**
 * Class TabbedLayout
 */
class TabbedLayout extends CWidget
{

	/**
	 * @var array
	 */
	public $tabs = array();
	/**
	 * @var
	 */
	public $activeTab;
	/**
	 * @var
	 */
	public $id;
	/**
	 * @var
	 */
	public $sessionKey;
	/**
	 * @var
	 */
	protected $lastTab;

	/**
	 *
	 */
	public function run()
	{

		reset($this->tabs);
		if (!$this->activeTab) {
			$this->activeTab = key($this->tabs);
		}
		/** @var $session CHttpSession */
		if (isset(Yii::app()->session[$this->sessionKey]) && Yii::app()->session[$this->sessionKey]) {
			$this->activeTab = Yii::app()->session[$this->sessionKey];
		}

		$this->render("_tabbedLayout", ['thisWidget' => $this]);

	}

	/**
	 *
	 */
	public function init()
	{

		$this->id         = $this->id ? : $this->generateId();
		$this->sessionKey = $this->getId() . "_" . $this->id;

		if (!isset(Yii::app()->session[$this->sessionKey]) || !Yii::app()->session[$this->sessionKey]) {
			Yii::app()->session[$this->sessionKey] = '';
		}
	}

	/**
	 * @param       $header
	 * @param array $htmlOptions
	 */
	public function beginTab($header, $htmlOptions = array())
	{

		if (!isset($htmlOptions['class'])) {
			$htmlOptions['class'] = 'tab';
		} else {
			$htmlOptions['class'] .= ' tab';
		}

		if (!isset($htmlOptions['id']) || !$htmlOptions['id']) {
			$htmlOptions['id'] = $this->generateTabId();
		}

		$this->tabs[$htmlOptions['id']] = ['header' => $header, 'htmlOptions' => $htmlOptions, 'content' => ''];
		$this->lastTab                  = $htmlOptions['id'];
		ob_start();
	}

	/**
	 *
	 */
	public function endTab()
	{

		$this->tabs[$this->lastTab]['content'] = ob_get_clean();
	}

	/**
	 * @return string
	 */
	public function generateTabId()
	{

		static $x = 0;
		return $this->id . '_tab' . $x++;
	}

	/**
	 * @return string
	 */
	public function generateId()
	{

		static $x = 0;
		return 'tabgr' . $x++;
	}
}