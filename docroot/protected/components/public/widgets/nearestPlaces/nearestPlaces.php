<?php

class nearestPlaces extends CWidget
{
	public $view = "";
	public $lat;
	public $lng;
	public $maxDistance;	//in meters
	public $count;
	public $mapObject = NULL;
	public $mapJourneyButton = false;
	public $id;

	public $maxDistKm;
	public $newLat;
	public $newLng;
	public $radius = 6371;	// earth's radius, km

	public function init()
	{
		parent::init();

		$this->maxDistKm = $this->maxDistance / 1000 ;
		$this->newLat = deg2rad($this->lat);
		$this->newLng = deg2rad($this->lng);
	}


	public function run()
	{
		parent::run();

		$criteria = "(acos(sin($this->newLat)*sin(radians(latitude)) + cos($this->newLat)*cos(radians(latitude))*cos(radians(longitude)-($this->newLng)))*$this->radius)";
		$limit = (!empty($this->count)) ? " LIMIT " . $this->count : "";

		$command = Yii::app()->db->createCommand("SELECT  *, ".$criteria." AS 'distance' FROM transportStations WHERE ".$criteria." <= ".$this->maxDistKm." ORDER BY " . $criteria . $limit);
		$transportStations = $command->queryAll();

		$this->render($this->view ? $this->view : 'default',['transportStations'=>$transportStations,
										 'thisLatitude'=>$this->lat,
										 'thisLongitude'=>$this->lng,
										 'mapObject'=>$this->mapObject,
										 'mapJourneyButton'=>$this->mapJourneyButton,
										 'id' => $this->id,
										 ]);
	}

}