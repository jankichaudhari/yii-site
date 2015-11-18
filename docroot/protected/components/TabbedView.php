<?php
class TabbedView extends CWidget
{

	public $tabs = array();
	public $activeTab;
	public $id;
	protected $lastTab;

	public $cssFile;

	public function run()
	{

		reset($this->tabs);
		if (!$this->activeTab) {
			$this->activeTab = key($this->tabs);
		}
		echo '<div class="tab-group">';
		echo '<div class="tab-group-header">';
		foreach ($this->tabs as $id => $tab) {
			echo '<span class="tab-header ' . ($this->activeTab == $id ? 'active' : '') . '" data-header-for="' . $id . '">' . $tab['header'] . '</span>';
		}
		echo '</div>';
		echo '<div class="tab-container">';
		foreach ($this->tabs as $id => $tab) {
			$tab['htmlOptions']['data-tab-id'] = $tab['htmlOptions']['id'];
			if ($id == $this->activeTab) {
				$tab['htmlOptions']['class'] .= ' active';
			}
			echo CHtml::tag('div', $tab['htmlOptions'], $tab['content']);
		}
		echo '</div>';
		echo '</div>';

		/** @var $cs CClientScript */
		$cs = Yii::app()->getClientScript();
		$cs->registerScriptFile('/js/TabbedView.js', CClientScript::POS_END);

	}

	public function init()
	{
		if (!$this->cssFile) {
			$this->cssFile = '/css/TabbedView.css';
		}
		/** @var $cs CClientScript */
		$cs = Yii::app()->getClientScript();
		$cs->registerCssFile($this->cssFile);
		$this->id = $this->id ? : $this->generateId();
	}

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

	public function endTab()
	{

		$this->tabs[$this->lastTab]['content'] = ob_get_clean();
	}

	public function generateTabId()
	{

		static $x = 0;
		return $this->id . '_tab' . $x++;
	}

	public
	function generateId()
	{

		static $x = 0;
		return 'tabgr' . $x++;
	}
}