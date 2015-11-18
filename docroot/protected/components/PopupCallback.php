<?php
class PopupCallback
{

	protected $callBackFunction;

	public function __construct($callBackFunction)
	{

		$this->callBackFunction = $callBackFunction;
	}

	/**
	 * @param  Array   $arguments Arguments to be passed to parent widnow or opener
	 * @param bool     $end
	 */
	public function run($arguments, $end = true)
	{

		if (!$this->callBackFunction) return;
		$js = '
			if(window.parent && window.parent.window && window.parent.window.' . $this->callBackFunction . ') {
				window.parent.window.' . $this->callBackFunction . '("' . implode('", "', $arguments) . '");
			} else if(window.opener && window.opener.window && window.opener.window.' . $this->callBackFunction . ') {
				window.opener.window.' . $this->callBackFunction . '("' . implode('", "', $arguments) . '");
				' . ($end ? 'window.close();' : '') . '
	}';

		if ($end) {
			echo '<script>' . $js . '</script>';
			Yii::app()->end();
		} else {
			/** @var $cs CClientScript */
			$cs = Yii::app()->getClientScript();
			$cs->registerScript('PopupCallback_' . $this->callBackFunction, $js, CClientScript::POS_END);
		}
	}

}