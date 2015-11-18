<?php
class m120829_152329_alter_table_transportStations_add_data_from_table_Place extends CDbMigration
{
	public function up()
	{
		$sql0 = Yii::app()->db->createCommand("SELECT  * FROM transportStations" )->queryAll();
		if(count($sql0)!=0){
			Yii::app()->db->createCommand("truncate table  transportStations")->execute();
			$res2 = Yii::app()->db->createCommand("truncate table  link_transportStations_to_transportTypes")->execute();
		}
		$transportStationsTitle = Yii::app()->db->createCommand("Show columns from transportStations WHERE Field = 'title'")->queryAll();
		if($transportStationsTitle[0]['Key']=='UNI'){
			@Yii::app()->db->createCommand("ALTER TABLE transportStations DROP INDEX title")->execute();
		}
		@Yii::app()->db->createCommand("ALTER TABLE transportStations ADD UNIQUE (title)")->execute();

		$command = Yii::app()->db->createCommand("SELECT  * FROM places");
		$places = $command->queryAll();
		$i = 0;
		foreach($places as $place){
			$type = $place['place_type'];
			$title = $place['place_title'];
			$desc = $place['place_desc'];
			$objCoOrdinates = new OSRef($place['place_osx'],$place['place_osy']);
			$coOrdinates = $objCoOrdinates->toLatLng();
			// convert in lat ang lng and insert in to table transportStations
			$lat = $coOrdinates->lat;
			$lng = $coOrdinates->lng;
			$user = 0;
			$dtTm = date('Y-m-d H:i:s');

			$sql1 = Yii::app()->db->createCommand("SELECT  * FROM transportStations WHERE title = '$title' " )->queryAll();

			if(count($sql1)==0){
				$sql = "INSERT INTO transportStations(title,description,latitude,longitude,createdBy,createdDt,modifiedBy,modifiedDt,statusId) VALUES ('$title','$desc','$lat','$lng','$user','$dtTm','$user','$dtTm','1')";
				$command = Yii::app()->db->createCommand($sql);
				$res = $command->execute();
				// convert in lat ang lng and insert in to table transportStations
				// insert all types in table link_transportStations_to_transportTypes
				if(!empty($res)){
					$i++;
					$thisType = ($type==1) ? 2 : 1 ;
					$sql = "INSERT INTO link_transportStations_to_transportTypes(transportStation,transportType,status) VALUES ('$i','$thisType','1')";
					Yii::app()->db->createCommand($sql)->execute();
				}
			}
			// insert all types in table link_transportStations_to_transportTypes
		}

		// Import file
		$csvFile       = Yii::getPathOfAlias('application.data') . '/transport_station_positions.csv';
		$importFileSql = "LOAD DATA LOCAL INFILE '" . $csvFile . "'
							REPLACE
							INTO TABLE transportStations
							COLUMNS TERMINATED BY ','
							OPTIONALLY ENCLOSED BY '\"'
							LINES TERMINATED BY '|'
							IGNORE 0 LINES";
		Yii::app()->db->createCommand($importFileSql)->execute();

		$newStations = Yii::app()->db->createCommand("SELECT  * FROM transportStations WHERE id > " . $i . " ORDER BY id ASC ")->queryAll();
		foreach($newStations as $newStation){
			$stationId = $newStation['id'];
			$sql2 = "INSERT INTO link_transportStations_to_transportTypes(transportStation,transportType,status) VALUES ('$stationId','1','1')";
			Yii::app()->db->createCommand($sql2)->execute();
		}

		return true;
	}

	public function down()
	{
		$sql1 = "truncate table  transportStations";
		$res1 = Yii::app()->db->createCommand($sql1)->execute();

		$sql2 = "truncate table  link_transportStations_to_transportTypes";
		$res2 = Yii::app()->db->createCommand($sql2)->execute();

		$transportStationsTitle = Yii::app()->db->createCommand("Show columns from transportStations WHERE Field = 'title'")->queryAll();
		if($transportStationsTitle[0]['Key']=='UNI'){
			@Yii::app()->db->createCommand("ALTER TABLE transportStations DROP INDEX title")->execute();
		}

		if($res1 && $res2)
		return true;
	}
}