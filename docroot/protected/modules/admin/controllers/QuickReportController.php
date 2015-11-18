<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vitaly.suhanov
 * Date: 02/04/12
 * Time: 14:41
 */

class QuickReportController extends AdminController
{
	public function accessRules()
	{
		$b          = parent::accessRules();
		$mergeArray = CMap::mergeArray(array(
											array('allow', 'actions' => ['hmrc'], 'users' => array("@")),
									   ), $b);
		return $mergeArray;
	}

	public function filters()
	{

		return CMap::mergeArray(parent::filters(),
								array('superAdminOnly + Create, List, View', 'guestView')
		);
	}

	/**
	 * @view QuickReport/list.php
	 */
	public function actionList()
	{

		$model = new QuickReport('search');
		if (isset($_POST['QuickReport']) && $_POST['QuickReport']) {
			$model->attributes = $_POST['QuickReport'];
		}

		$dataProvider = $model->search();
		$this->render("list", compact('dataProvider'));
	}

	/**
	 * @view QuickReport/create_delete.php
	 */
	public function actionCreate()
	{

		$model = new QuickReport();

		if (isset($_POST['QuickReport']) && $_POST['QuickReport']) {
			$model->attributes = $_POST['QuickReport'];
			if ($model->save()) {
				$this->redirect(array(
									 'update',
									 'pk' => $model->name
								), true);
			}
		}
		$this->render("edit", array('model' => $model));
	}

	public function actionUpdate()
	{

		$model = QuickReport::model()->findByPk($_GET['pk']);

		if (!$model) {
			throw new CHttpException("Can not find model specified by pk " . $_GET['pk'] . "");
		}

		if (isset($_POST['QuickReport'])) {
			$model->attributes = $_POST['QuickReport'];
			if ($model->save()) {
				$this->redirect(array(
									 'update',
									 'pk' => $model->name
								), true);
			}
		}

		$this->render("edit", array('model' => $model));
	}

	/**
	 * @view QuickReport/view.php
	 * @param $pk
	 * @throws CHttpException
	 */
	public function actionView($pk)
	{

		/** @var $model QuickReport */
		$model = QuickReport::model()->findByPk($pk);

		if (!$model) {
			throw new CHttpException("Can not find model specified by pk " . $pk . "");
		}
		$count        = Yii::app()->db->createCommand('SELECT COUNT(*) FROM (' . $model->query . ') as temp_select')->queryScalar();
		$dataProvider = new CSqlDataProvider($model->query, array(
																 'totalItemCount' => $count,
																 'pagination'     => array('pageSize' => 37),
																 'keyField'       => $model->keyField,
															));

		$this->render("view", array('dataProvider' => $dataProvider, 'model' => $model));

	}

	/**
	 * @view QuickReport/propertyStat.php
	 */
	public function actionInstructionsWithoutEpc()
	{

		$statuses     = array('Available', 'Under Offer', 'Proofing', 'Instructed', 'Production');
		$where        = "WHERE d.dea_status in ('" . implode("', '", $statuses) . "') AND (m.orientation = 1 OR m.med_id is NULL) AND (d.dea_psubtype NOT IN (26, 16, 17))";
		$sqlQuery     = "SELECT m.orientation AS has_old_epc,
						`p`.`pro_id` AS `property_id`,
						`m`.`med_id` AS `epc_count`,
						CONCAT(`addr`.`line1`,' ',`addr`.`line2`,' ',`addr`.`line3`,' ',`addr`.`line4`,' ', `addr`.`line5`) AS `address`,
						`d`.`dea_id` AS `deal_id`,
						`d`.`dea_type` AS `deal_type`,
						`addr`.`postcode` AS `postcode`,
						`d`.`dea_status` AS `status`,
						CASE WHEN d.dea_status IN ('Available', 'Under Offer' ) THEN MAX(`s`.`sot_date`) END AS `last_available`,
						CASE WHEN d.dea_status IN ('Available', 'Under Offer' ) THEN (TO_DAYS(NOW()) - TO_DAYS(MAX(`s`.`sot_date`))) END AS `days_on_site`,
						 MAX(`s1`.`sot_date`) AS `status_date`,
						CONCAT(ptype.pty_title, ', ', subtype.pty_title) as prop_type,
						office.code as `officeCode`,
						GROUP_CONCAT(DISTINCT CONCAT(cli_fname,' ',cli_sname) SEPARATOR ', ')  AS 'owner'
					FROM `deal` `d`
					LEFT JOIN `media` `m` ON (`m`.`med_row` = `d`.`dea_id`) AND (`m`.`med_type` = 'EPC')
					LEFT JOIN `property` `p` ON (`p`.`pro_id` = `d`.`dea_prop`)
					LEFT JOIN `address` `addr` ON (`addr`.`id` = `p`.`addressId`)
					LEFT JOIN `sot` `s1` ON (`d`.`dea_id` = `s1`.`sot_deal`)
					LEFT JOIN `sot` `s` ON(`d`.`dea_id` = `s`.`sot_deal`) AND (`s`.`sot_status` = 'available')
					LEFT JOIN `ptype` ON (d.dea_ptype = ptype.pty_id)
					LEFT JOIN `ptype` as subtype ON (d.dea_psubtype = subtype.pty_id)
					LEFT JOIN `branch` as branch ON (d.dea_branch = branch.bra_id)
					LEFT JOIN `office` as office ON (branch.office_id = office.id)

					LEFT JOIN link_client_to_instruction ON link_client_to_instruction.dealId = d.dea_id AND link_client_to_instruction.capacity = 'Owner'
					LEFT JOIN client ON link_client_to_instruction.clientId = client.cli_id
					" . $where . "
					GROUP BY `d`.`dea_id`";
		$count        = Yii::app()->db->createCommand('SELECT COUNT(*) FROM (' . $sqlQuery . ') as temp_select')->queryScalar();
		$dataProvider = new CSqlDataProvider($sqlQuery, array(
															 'totalItemCount' => $count,
															 'pagination'     => array("pageSize" => 37),
															 'keyField'       => 'deal_id',
															 'sort'           => array(
																 'attributes' => array(
																	 'address', 'deal_id', 'propert`y_id', 'postcode',
																	 'status', 'last_available', 'deal_type',
																	 'days_on_site', 'status_date', 'prop_type',
																	 'owner', 'officeCode'
																 ),
																 'multiSort'  => true
															 ),
														));

		$this->render("propertyStat", array('dataProvider' => $dataProvider));
	}

	public function actionHmrc()
	{
		$model = new HmrcForm();
		if (isset($_GET['HmrcForm']) && $_GET['HmrcForm']) {
			$model->attributes = $_GET['HmrcForm'];
		}
		$sql        = "SELECT a.app_id, a.app_user, a.app_start, a.app_end,u.use_branch, p.pro_id, u.use_fname, u.use_sname, addr.lat, addr.lng, addr.searchString AS address FROM appointment a
		INNER JOIN link_deal_to_appointment l1 ON l1.d2a_app = a.app_id
		INNER JOIN deal d ON l1.d2a_dea = d.dea_id
		INNER JOIN property p ON d.dea_prop = p.pro_id
		INNER JOIN address addr ON addr.id = p.addressId
		INNER JOIN `user` u ON a.app_user = u.use_id
		WHERE a.app_type IN('Viewing','Valuation')
		AND a.app_status NOT IN('Cancelled', 'Deleted')
		AND a.app_start BETWEEN :dateFrom AND :dateTo
		AND p.pro_latitude IS NOT NULL AND p.pro_longitude IS NOT NULL
		ORDER BY use_id, a.app_start";
		$dataReader = Yii::app()->db->createCommand($sql)->queryAll(true, ['dateFrom' => $model->dateFrom, 'dateTo' => $model->dateTo]);

		$data    = [];
		$prevApp = null;
		$nextApp = null;
		$thisApp = null;

		for ($i = 0; $i < count($dataReader); $i++) {
			$thisApp = $dataReader[$i];
			if ($i + 1 < count($dataReader)) $nextApp = $dataReader[$i + 1];
			if ($i) $prevApp = $dataReader[$i - 1];
			$data[] = $this->processHmrc($prevApp, $thisApp, $nextApp, $model);

		}
		if (isset($_GET['export'])) {

			$filename = Yii::app()->params['tmpDirPath'] . "/HMRC_expenses_" . date("dmY") . ".csv";
			/** @var $file CFile */
			$file = Yii::app()->file->set($filename);
			$file->create();
			$file->setMimeType("application/vnd.ms-excel");
			$file->setContents($this->hmrcToCSV($data))->download();
			Yii::app()->end();
		}

		$dataProvider = new CArrayDataProvider($data, array('pagination' => ['pageSize' => 100]));
		$this->render('hmrc', compact('data', 'dataProvider', 'model'));
	}

	private function calcDistance($fromLat, $fromLng, $toLat, $toLng)
	{
		$fromLat = deg2rad($fromLat);
		$fromLng = deg2rad($fromLng);
		$toLat   = deg2rad($toLat);
		$toLng   = deg2rad($toLng);
		return round(acos(sin($fromLat) * sin($toLat) + cos($fromLat) * cos($toLat) * cos($toLng - $fromLng)) * 6371, 5);
	}

	private function processHmrc($prevApp, $thisApp, $nextApp, HmrcForm $model)
	{
		static $i = 0;
		$branchCords                    = array(
			1 => [51.472003, -0.088445], // cam.sale
			2 => [51.431805, -0.060736], // syd.sale
			3 => [51.472003, -0.088445], // cam.let
			4 => [51.431805, -0.060736], // syd.let
			5 => [51.472003, -0.088445], // whitstable. consider cam
			6 => [51.472003, -0.088445], // cam maintanance
			7 => [51.459462, -0.125275], // brixton
			8 => [51.459462, -0.125275], // brixton
		);
		$minTimeBetweenAppsToGoToOffice = $model->timeBetweenApps;

		if (!$prevApp
				|| $prevApp['app_user'] != $thisApp['app_user']
				|| abs((strtotime($thisApp['app_start']) - strtotime($prevApp['app_end'])) / 60) > $minTimeBetweenAppsToGoToOffice
		) { // prev app is the office
			$prevApp            = [];
			$prevApp['lat']     = $branchCords[$thisApp['use_branch']][0];
			$prevApp['lng']     = $branchCords[$thisApp['use_branch']][1];
			$prevApp['address'] = 'Office';
		}

		if ($nextApp && date("H", strtotime($thisApp['app_end'])) >= 18 && date("d", strtotime($nextApp['app_start'])) != date('d', strtotime($thisApp['app_end']))) {
			$nextApp              = [];
			$nextApp['address']   = 'Home';
			$nextApp['app_start'] = 0;
		} elseif (!$nextApp
				|| $nextApp['app_user'] != $thisApp['app_user']
				|| abs((strtotime($nextApp['app_start']) - strtotime($thisApp['app_end'])) / 60) > $minTimeBetweenAppsToGoToOffice
		) { // next app will be the office
			$nextApp              = [];
			$nextApp['lat']       = $branchCords[$thisApp['use_branch']][0];
			$nextApp['lng']       = $branchCords[$thisApp['use_branch']][1];
			$nextApp['address']   = 'Office';
			$nextApp['app_start'] = 0;
		}
		return array(
			'id'                    => $i++,
			'user'                  => $thisApp['use_fname'] . ' ' . $thisApp['use_sname'],
			'app_id'                => $thisApp['app_id'],
			'address'               => $thisApp['address'],
			'coming_from'           => $prevApp['address'],
			'going_to'              => $nextApp['address'],
			'branch'                => $thisApp['use_branch'],
			'distanceFromPrevPlace' => $nextApp['address'] != 'Home' ? $this->calcDistance($prevApp['lat'], $prevApp['lng'], $thisApp['lat'], $thisApp['lng']) : 0,
			'distanceToOffice'      => $nextApp['address'] == 'Office' ? $this->calcDistance($thisApp['lat'], $thisApp['lng'], $nextApp['lat'], $nextApp['lng']) : 0,
			'time_to_next'          => (strtotime($nextApp['app_start']) - strtotime($thisApp['app_end'])) / 60,
			'time'                  => date("d/m/Y H:i", strtotime($thisApp['app_start'])),
			'end_time'              => date("d/m/Y H:i", strtotime($thisApp['app_end'])),
			'next_time'             => $nextApp['app_start'] ? date("d/m/Y H:i", strtotime($nextApp['app_start'])) : '',
		);
	}

	private function hmrcToCSV($data)
	{
		array_unshift($data, array_combine($t = array_keys($data[0]), $t));
		return implode("\n", array_map(function ($d) {
			$str = '"' . implode('","', $d) . '"';
			return $str;
		}, $data));

	}

}
