<?php

class ConsoleCommand extends CConsoleCommand
{
	protected $outputLog = false;
	protected $_log = [];

	public function run($args)
	{
		if (in_array('verbose', $args)) {
			$this->outputLog = true;
		}
		parent::run($args);
	}

	public function log($message)
	{
		$this->_log[] = $message;
	}

	public function afterAction($action, $params, $exitCode = 0)
	{
		if ($this->outputLog) {
			echo implode("\n", $this->_log) . "\n";
		}
		array_unshift($this->_log, 'Launched at: ' . date("Y-m-d H:i:s"));
		file_put_contents(Yii::app()->params['logDirPath'] . '/' . get_class($this) . '.log', implode("\n", $this->_log) + "\n", FILE_APPEND);
		return parent::afterAction($action, $params, $exitCode);
	}
}
