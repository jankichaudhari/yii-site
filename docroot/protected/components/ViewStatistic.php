<?php
class ViewStatistic
{
	var $name;
	var $table;
	var $pageViewTable;
	var $viewStatisticSession;
	var $sessionId;

	/** @var PDO */
	var $pdo;

	/** @var CHttpSession|array */
	var $session;

	var $useYii;

	public function __construct($name = '', $table = 'stat_fullViewStatistic', $pageViewTable = 'stat_pageViewStatistic')
	{

		$this->table         = $table;
		$this->pageViewTable = $pageViewTable;
		$this->name          = 'viewStatistic';
		if ($name) {
			$this->name .= '_' . $name;
		}

		if(class_exists("Yii")) { // in case of yii
			$this->session = Yii::app()->session;
			$this->sessionId = $this->session->sessionID;
			$this->useYii = true;
			$this->pdo = Yii::app()->db->getPdoInstance();
		} else {
			$this->session = &$_SESSION;
			$this->sessionId = session_id();
			$this->pdo = PDOObject::getInstance();
		}

		if (!isset($this->session[$this->name])) {
			$this->session[$this->name] = array('viewedPages' => array());
		}
		$this->viewStatisticSession = $this->session[$this->name];
	}

	public function run()
	{

		$t          = explode("?", $_SERVER['REQUEST_URI']);
		$requestURI = reset($t);

		if (!in_array($requestURI, $this->viewStatisticSession['viewedPages'])) {



			$this->viewStatisticSession['viewedPages'][] = $requestURI;

			$sql = "INSERT INTO " . $this->table . " SET
								requestURI = ?,
								queryString = ?,
								requestMethod = ?,
								scriptName = ?,
								userAgent = ?,
								ip = ?,
								referer = ?,
								session = ?,
								phpSelf = ?";

			$command = $this->pdo->prepare($sql);
			$result  = $command->execute(array(
																	  $requestURI,
																	  $_SERVER['QUERY_STRING'],
																	  $_SERVER['REQUEST_METHOD'],
																	  $_SERVER['SCRIPT_NAME'],
																	  $_SERVER['HTTP_USER_AGENT'],
																	  $_SERVER['REMOTE_ADDR'],
																	  (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : ""),
																	  $this->sessionId,
																	  $_SERVER['PHP_SELF'],
																 ));
			$id      = $this->pdo->lastInsertId();

			$sql     = "INSERT INTO " . $this->pageViewTable . "
						SET page = :page,
							viewCount = viewCount + 1,
							lastViewId = :viewId ON DUPLICATE KEY UPDATE
							lastViewId = :viewId,
							viewCount = viewCount + 1";
			$command = $this->pdo->prepare($sql);
			$command->execute(array('page' => $requestURI, 'viewId' => $id));

			$this->saveSession();
		}
		return true;
	}

	private function saveSession()
	{
		$this->session[$this->name] = $this->viewStatisticSession;
		if(!$this->useYii) {
			$_SESSION[$this->name] = $this->viewStatisticSession;
		}
	}


}