<?php
define("FEED_NAME", "zoomf");
require_once(dirname(__FILE__) . "/../../../../config/config_feed.inc.php");

class FeedCommand extends CConsoleCommand
{
	public function run($args)
	{
		parent::run($args);
	}

	public function actionIndex()
	{

	}

	public function actionZoopla()
	{

	}

	public function actionZoomf()
	{

		global $CONFIG;
		$feedName = "zoomf";
		$strPath  = $this->getFeedFolderPath($feedName);

		$command    = Yii::app()->db->createCommand($this->getSQLStatement());
		$dataReader = $command->query();
		$RENDER     = $this->getFeedHeader($dataReader);
		foreach ($dataReader as $row) {
			switch ($row['dea_branch']) {
				case 2 :
					$intRMBranchID = 24869;
					break;
				case 4 :
					$intRMBranchID = 24869;
					break;
				case 1 :
				case 3 :
				default :
					$intRMBranchID = 3120;
					break;
			}

			$postcode = explode(" ", $row["pro_postcode"]);

			$RENDER .= $intRMBranchID . "_" . $row["dea_id"] . "^"; //AGENT_REF
			$RENDER .= $row["pro_addr1"] . "^"; //ADDRESS_1
			$RENDER .= $row["pro_addr3"] . "^"; //ADDRESS_2
			$RENDER .= $row["are_title"] . "^"; //ADDRESS_3
			$RENDER .= "^"; //ADDRESS_4
			$RENDER .= $row["pro_addr5"] . "^"; //TOWN

			$RENDER .= $postcode[0] . "^"; //POSTCODE1
			$RENDER .= $postcode[1] . "^"; //POSTCODE2

			$RENDER .= "^"; //FEATURE1 property type
			$RENDER .= "^"; //FEATURE2 bedroms
			$RENDER .= "^"; //FEATURE3 ch
			$RENDER .= "^"; //FEATURE4 dg
			$RENDER .= "^"; //FEATURE5
			$RENDER .= "^"; //FEATURE6
			$RENDER .= "^"; //FEATURE7
			$RENDER .= "^"; //FEATURE8
			$RENDER .= "^"; //FEATURE9
			$RENDER .= "^"; //FEATURE10

			$length = (300 - strlen($row["dea_strapline"])); // 300 is allowed, but i add (cont) so we use 294

			$desc    = $this->htmlentitiesWithStripTag($row["dea_description"]);
			$trimmed = preg_replace("/[\r\n]+[\s\t]*[\r\n]+/", "", $desc);
			$trimmed = str_replace("&amp;#039;", "'", $trimmed);
			$trimmed = str_replace("&amp;amp;#039;", "'", $trimmed);
			$trimmed = str_replace("&amp;eacute;", "�", $trimmed);

			$trimmed = substr($trimmed, 0, $length);

			$RENDER .= $row["dea_strapline"] . ": " . $trimmed . "^"; //SUMMARY

			$longDescription = $row["dea_description"];
			if ($row["total_area"]) {
				$longDescription .= "<p>Approximate Gross Internal Area: " . $row["total_area"] . " square metres</p>";
			}
			$longDescription .= "<p>For further information or to arrange a viewing, please contact our <b>" . $row["bra_title"] . " Branch</b> on <b>" . $row["bra_tel"] . ".</b></p>";
			$longDescription .= "<p>Visit <b>www.woosterstock.co.uk</b> for full details, colour photos, maps and floor plans.</p>";
			$longDescription .= "<p>We endeavour to make all our property particulars, descriptions, floor-plans, marketing and local information accurate and reliable but we make no guarantees as to the accuracy of this information. All measurements and dimensions are for guidance only and should not be considered accurate. If there is any point which is of particular importance to you we advise that you contact us to confirm the details; particularly if you are contemplating travelling some distance to view the property. Please note that we have not tested any services or appliances mentioned in property sales details.</p>";

			$RENDER .= preg_replace("/[\r\n]+[\s\t]*[\r\n]+/", "", $longDescription) . "^"; //DESCRIPTION

			$RENDER .= $intRMBranchID . "^"; //BRANCH_ID

			// we have three possible sattusses : under Offer statuses returns 1; 0 otherwise
			($row["dea_status"] == 'Under Offer' || $row["dea_status"] == 'Under Offer with Other') ? $RENDER .= "1" . "^" : $RENDER .= "0" . "^";

			$RENDER .= $row["dea_bedroom"] . "^"; //BEDROOMS
			$RENDER .= $row["dea_marketprice"] . "^"; //PRICE
			$RENDER .= "0" . "^"; //PRICE_QUALIFIER

			if ($row["dea_psubtype"] == 4) { //PROP_SUB_ID
				$RENDER .= "4" . "^";
			}
			elseif ($row["dea_psubtype"] == 5) {
				$RENDER .= "3" . "^";
			}
			elseif ($row["dea_psubtype"] == 6) {
				$RENDER .= "1" . "^";
			}
			elseif ($row["dea_psubtype"] == 7) {
				$RENDER .= "2" . "^";
			}
			elseif ($row["dea_psubtype"] == 8) {
				$RENDER .= "5" . "^";
			}
			elseif ($row["dea_psubtype"] == 9) {
				$RENDER .= "12" . "^";
			}
			elseif ($row["dea_psubtype"] == 10) {
				$RENDER .= "26" . "^";
			}
			elseif ($row["dea_psubtype"] == 11) {
				$RENDER .= "29" . "^";
			}
			elseif ($row["dea_psubtype"] == 12) {
				$RENDER .= "11" . "^";
			}
			elseif ($row["dea_psubtype"] == 13) {
				$RENDER .= "9" . "^";
			}
			elseif ($row["dea_psubtype"] == 14) {
				$RENDER .= "19" . "^";
			}
			elseif ($row["dea_psubtype"] == 15) {
				$RENDER .= "19" . "^";
			}
			elseif ($row["dea_psubtype"] == 16) {
				$RENDER .= "51" . "^";
			}
			elseif ($row["dea_psubtype"] == 17) {
				$RENDER .= "20" . "^";
			}
			elseif ($row["dea_psubtype"] == 19) {
				$RENDER .= "28" . "^";
			}
			elseif ($row["dea_psubtype"] == 20) {
				$RENDER .= "28" . "^";
			}
			elseif ($row["dea_psubtype"] == 21) {
				$RENDER .= "28" . "^";
			}
			elseif ($row["dea_psubtype"] == 22) {
				$RENDER .= "23" . "^";
			}
			elseif ($row["dea_psubtype"] == 23) {
				$RENDER .= "28" . "^";
			}
			elseif ($row["dea_psubtype"] == 25) {
				$RENDER .= "21" . "^";
			}
			elseif ($row["dea_psubtype"] == 26) {
				$RENDER .= "48" . "^";
			}
			elseif ($row["dea_psubtype"] == 27) {
				$RENDER .= "19" . "^";
			}
			elseif ($row["dea_psubtype"] == 28) {
				$RENDER .= "19" . "^";
			}
			elseif ($row["dea_psubtype"] == 29) {
				$RENDER .= "19" . "^";
			}
			// defaults for other and new subtypes (use main type)
			else {
				if ($row["dea_ptype"] == 1) {
					$RENDER .= "26" . "^";
				}
				elseif ($row["dea_ptype"] == 2) {
					$RENDER .= "28" . "^";
				}
				elseif ($row["dea_ptype"] == 3) {
					$RENDER .= "19" . "^";
				}
			}

			$RENDER .= $row["dea_launchdate"] . "^"; //CREATE_DATE
			$RENDER .= date("Y-m-d H:i:s") . "^"; //UPDATE_DATE

			$RENDER .= $row["pro_addr3"] . ", " . $row["are_title"] . ", " . $postcode[0] . "^"; //DISPLAY_ADDRESS
			$RENDER .= "1" . "^"; //PUBLISHED_FLAG

			if ($row["dea_type"] == 'Lettings') { //LET_DATE_AVAILABLE
				$RENDER .= $row["dea_available"] . "^";
			} else {
				$RENDER .= "^";
			}

			$RENDER .= "^"; //LET_BOND

			if ($row["PriceType"] == 1) { //LET_TYPE_ID
				$RENDER .= "1" . "^";
			} elseif ($row["PriceType"] == 2) {
				$RENDER .= "2" . "^";
			} else {
				$RENDER .= "0" . "^";
			}

			if ($row["furnished"] == 1) { //LET_FURN_ID
				$RENDER .= "2" . "^";
			} elseif ($row["furnished"] == 2) {
				$RENDER .= "1" . "^";
			} elseif ($row["furnished"] == 3) {
				$RENDER .= "0" . "^";
			} else {
				$RENDER .= "3" . "^";
			}

			$RENDER .= "0" . "^"; //LET_RENT_FREQUENCY

			if ($row["dea_type"] == 'Sales') { //TRANS_TYPE_ID
				$RENDER .= "1" . "^";
			} else {
				$RENDER .= "2" . "^";
			}

			$image_path = WS_PATH_IMAGES . '/' . $row["dea_id"] . '/';

			if ($row["photos"]) {
				$photo_array = explode("~", $row["photos"]);
			}
			/* $max_images = sizeof($photo_array); */
			$max_images = 10;

			for ($i = 0; $i <= $max_images; $i++) {

				if ($photo_array[$i]) {
					$photo = explode("|", $photo_array[$i]);

					if (file_exists($image_path . str_replace(".jpg", "_large.jpg", $photo[0]))) {
						$source_image = $image_path . str_replace(".jpg", "_large.jpg", $photo[0]);
					}
					elseif (file_exists($image_path . str_replace(".jpg", "_small.jpg", $photo[0]))) {
						$source_image = $image_path . str_replace(".jpg", "_small.jpg", $photo[0]);
					}

					$rm_image_name = $intRMBranchID . '_' . $row["dea_id"] . '_IMG_' . substr("0" . $i, -2) . '.jpg';

					// if the file already exists, delet before re-writing
					if (file_exists($strPath . "/" . $rm_image_name)) {
						unlink($strPath . "/" . $rm_image_name);
					}
					copy($source_image, $strPath . "/" . $rm_image_name);

					$RENDER .= $rm_image_name . "^"; //MEDIA_IMAGE_00
					$filesToDelete[] = $strPath . "/" . $rm_image_name;

					$RENDER .= $photo[1] . "^"; //MEDIA_IMAGE_TEXT_00

				} else { // no image

					$RENDER .= "^"; //MEDIA_IMAGE_00
					$RENDER .= "^"; //MEDIA_IMAGE_TEXT_00
				}

			}
			// epc document
			if ($row["epc"]) {
				$epc_array = explode("~", $row["epc"]);
			}

			$max_epc = 2;

			for ($i = 0; $i < $max_epc; $i++) {

				if ($epc_array[$i]) {
					$epc = explode("|", $epc_array[$i]);

					if (file_exists($image_path . str_replace(".jpg", "_large.jpg", $epc[0]))) {
						$source_image = $image_path . str_replace(".jpg", "_large.jpg", $epc[0]);
					}
					elseif (file_exists($image_path . str_replace(".jpg", "_small.jpg", $epc[0]))) {
						$source_image = $image_path . str_replace(".jpg", "_small.jpg", $epc[0]);
					}

					$rm_image_name = $intRMBranchID . '_' . $row["dea_id"] . '_IMG_' . substr("0" . ($i+60), -2) . '.jpg';

					// if the file already exists, delet before re-writing
					if (file_exists($strPath . "/" . $rm_image_name)) {
						unlink($strPath . "/" . $rm_image_name);
					}
					copy($source_image, $strPath . "/" . $rm_image_name);

					$RENDER .= $rm_image_name . "^"; //MEDIA_IMAGE_00
					$filesToDelete[] = $strPath . "/" . $rm_image_name;

					$RENDER .= $epc[1] . "^"; //MEDIA_IMAGE_TEXT_00

				} else { // no image

					$RENDER .= "^"; //MEDIA_IMAGE_00
					$RENDER .= "^"; //MEDIA_IMAGE_TEXT_00
				}

			}

			// floorplans
			if ($row["floorplans"]) {
				$floorplan_array = explode("~", $row["floorplans"]);
			}

			$max_floorplans = 5;

			for ($i = 0; $i <= $max_floorplans; $i++) {

				if ($floorplan_array[$i]) {
					$floorplan = explode("|", $floorplan_array[$i]);

					if (file_exists($image_path . $floorplan[0])) {
						$source_image = $image_path . $floorplan[0];
					}

					$rm_image_name = $intRMBranchID . '_' . $row["dea_id"] . '_FLP_' . substr("0" . $i, -2) . '.gif';

					// if the file already exists, delete before re-writing
					if (file_exists($strPath . "/" . $rm_image_name)) {
						unlink($strPath . "/" . $rm_image_name);
					}
					copy($source_image, $strPath . "/" . $rm_image_name);

					$RENDER .= $rm_image_name . "^"; //MEDIA_FLOOR_PLAN_01
					$filesToDelete[] = $strPath . "/" . $rm_image_name;

					$RENDER .= $floorplan[1] . "^"; //MEDIA_FLOOR_PLAN_TEXT_01

				} else { // no image

					$RENDER .= "^"; //MEDIA_FLOOR_PLAN_01
					$RENDER .= "^"; //MEDIA_FLOOR_PLAN_TEXT_01
				}

			}

			$RENDER = $this->removeLastChar($RENDER, "^");
			$RENDER = $this->removeLastChar($RENDER, "^");
			// brochure

			$render .= "http://" . WS_HOSTNAME . "/property/pdf/" . $row['dea_id'];

			$RENDER .= "~\n"; //End of record + line feed

		}
		$RENDER .= "#END#";

		$local_file = $strPath . "/" . date('Ymd') . ".txt";
		if (!file_put_contents($local_file, $RENDER)) {
			die("could not write to file");
		}
		$filesToDelete[] = $strPath . "/" . date("Ymd") . ".tar";
		$this->backupsus($strPath);

		// delete all files in array (that is all images and the blm text file)
		foreach ($filesToDelete as $filename) {
			@unlink($filename);
		}

		$dst_dir = $CONFIG[FEED_NAME]['ftp_destination'];
		$ftp     = new FTP($CONFIG[FEED_NAME]['ftp_server'], $CONFIG[FEED_NAME]['ftp_username'], $CONFIG[FEED_NAME]['ftp_password']);
		try {
			$ftp->upload($strPath, $dst_dir);
		} catch (Exception $e) {

		}
		file_put_contents(WS_PATH_LOGS . "/ftpUploadErrorLog.log", $ftp->getErrorLog(), FILE_APPEND);
		file_put_contents(WS_PATH_LOGS . "/ftpUploadMessageLog.log", $ftp->getMessageLog(), FILE_APPEND);

		// =================================================================================
		// <<< functions

	}

	public function getFeedHeader($dataReader)
	{
		return "#HEADER#
		Version : 3
		EOF : '^'
		EOR : '~'
		Property Count : " . $dataReader->rowCount . "
		Generated Date : " . date("Y-m-d H:i:s") . "
		#DEFINITION#
		AGENT_REF^ADDRESS_1^ADDRESS_2^ADDRESS_3^ADDRESS_4^TOWN^POSTCODE1^POSTCODE2^FEATURE1^FEATURE2^FEATURE3^
		FEATURE4^FEATURE5^FEATURE6^FEATURE7^FEATURE8^FEATURE9^FEATURE10^SUMMARY^DESCRIPTION^BRANCH_ID^
		STATUS_ID^BEDROOMS^PRICE^PRICE_QUALIFIER^PROP_SUB_ID^CREATE_DATE^UPDATE_DATE^DISPLAY_ADDRESS^
		PUBLISHED_FLAG^LET_DATE_AVAILABLE^LET_BOND^LET_TYPE_ID^LET_FURN_ID^LET_RENT_FREQUENCY^TRANS_TYPE_ID^
		MEDIA_IMAGE_00^MEDIA_IMAGE_TEXT_00^MEDIA_IMAGE_01^MEDIA_IMAGE_TEXT_01^MEDIA_IMAGE_02^MEDIA_IMAGE_TEXT_02^
		MEDIA_IMAGE_03^MEDIA_IMAGE_TEXT_03^MEDIA_IMAGE_04^MEDIA_IMAGE_TEXT_04^MEDIA_IMAGE_05^MEDIA_IMAGE_TEXT_05^
		MEDIA_IMAGE_06^MEDIA_IMAGE_TEXT_06^MEDIA_IMAGE_07^MEDIA_IMAGE_TEXT_07^MEDIA_IMAGE_08^MEDIA_IMAGE_TEXT_08^
		MEDIA_IMAGE_09^MEDIA_IMAGE_TEXT_09^MEDIA_IMAGE_10^MEDIA_IMAGE_TEXT_10^
		MEDIA_IMAGE_60^MEDIA_IMAGE_TEXT_60^MEDIA_IMAGE_61^MEDIA_IMAGE_TEXT_61^
		MEDIA_FLOOR_PLAN_01^MEDIA_FLOOR_PLAN_TEXT_01^MEDIA_FLOOR_PLAN_02^MEDIA_FLOOR_PLAN_TEXT_02^
		MEDIA_FLOOR_PLAN_03^MEDIA_FLOOR_PLAN_TEXT_03^MEDIA_FLOOR_PLAN_04^MEDIA_FLOOR_PLAN_TEXT_04^
		MEDIA_FLOOR_PLAN_05^MEDIA_FLOOR_PLAN_TEXT_05^MEDIA_DOCUMENT_01^
		~\n
		#DATA#\n";
	}

	public function getFeedFolderPath($feedName)
	{
		$folderPath = Yii::app()->params['FeedFilesPath'] . '/' . $feedName . '/' . date("Ymd") . "/";
		// let check if folder exists
		$this->createForlder($folderPath);
		return $folderPath;
	}

	public function backupsus($strPath)
	{
		global $backupdate, $backupdir, $backupto, $fileprefix, $tararg, $bz2arg, $files;
		$backupdate   = date("Ymd");
		$backupdir    = $strPath;
		$files        = "*";
		$backupto     = $strPath;
		$fileprefix   = "";
		$tararg       = "-cf";
		$backupsuscmd = "cd $backupdir;
				tar $tararg {$backupdate}.tar $files;
				gzip $bz2arg {$backupdate}.tar;
				mv {$backupdate}.tar.gz $backupto";
		passthru("$backupsuscmd");
	}

	/**
	 * this SQL statement is the same for all feeds we have at the momeent
	 * it is very stupid SQL query
	 *
	 * @return string
	 */
	public function getSQLStatement()
	{
		return "SELECT

			deal.*,
			area.are_title,
			pro_addr1,pro_addr3,pro_postcode,
			pro_east,pro_north,pro_latitude,pro_longitude,
			branch.bra_id,branch.bra_title,branch.bra_tel,branch.bra_fax,
			T.pty_title AS ptype,
			ST.pty_title AS psubtype,
			GROUP_CONCAT(DISTINCT CONCAT(feature.fea_title) ORDER BY feature.fea_id ASC SEPARATOR '~') AS features,
			GROUP_CONCAT(DISTINCT CONCAT(photos.med_file,'|',photos.med_title) ORDER BY photos.med_order ASC SEPARATOR '~') AS photos,
			GROUP_CONCAT(DISTINCT CONCAT(floorplans.med_file,'|',floorplans.med_title) ORDER BY floorplans.med_order ASC SEPARATOR '~') AS floorplans,
			GROUP_CONCAT(DISTINCT CONCAT(epc.med_file,'|',epc.med_title) ORDER BY epc.med_order ASC SEPARATOR '~') AS epc

		FROM deal

		LEFT JOIN property ON deal.dea_prop = property.pro_id
		LEFT JOIN area ON property.pro_area = area.are_id
		LEFT JOIN branch ON deal.dea_branch = branch.bra_id

		LEFT JOIN ptype AS T ON deal.dea_ptype = T.pty_id
		LEFT JOIN ptype AS ST ON deal.dea_psubtype = ST.pty_id

		LEFT JOIN media AS photos ON deal.dea_id = photos.med_row AND photos.med_table = 'deal' AND photos.med_type = 'Photograph'
		LEFT JOIN media AS floorplans ON deal.dea_id = floorplans.med_row AND floorplans.med_table = 'deal' AND floorplans.med_type = 'Floorplan'
		LEFT JOIN media AS epc ON deal.dea_id = epc.med_row AND epc.med_table = 'deal' AND epc.med_type = 'EPC'

		LEFT JOIN link_instruction_to_feature ON dealId = deal.dea_id
		LEFT JOIN feature ON featureId = feature.fea_id

		WHERE
		(deal.dea_status = 'Available' OR deal.dea_status = 'Under Offer' OR deal.dea_status = 'Under Offer with Other')

		GROUP BY dea_id
		";
	}

	private function createForlder($path)
	{
		$path = rtrim($path, " /") . "/";
		if (!file_exists($path) || !file_exists(dirname($path))) {
			createForlder(dirname($path));
			mkdir($path, 0777);
		}

	}

	private function removeLastChar($string, $char)
	{
		if(substr($string, -1) === $char) {
			return substr($string, 0, -1);
		}
		return $string;
	}

	private function htmlentitiesWithStripTag($string, $leaveTags = null)
	{
		return htmlentities(strip_tags($string, $leaveTags));
	}
}