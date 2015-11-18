<?php

class LocationController extends AdminController
{
	public function actionShowLocation()
	{
		$model = new Location();
		if(isset($_POST['Location'])){
			$model->attributes = $_POST['Location'];
			$model->scenario = 'mandatory';
			if($model->save()){
				echo 'saved';
			}
		}

		$this->render('_location_form', array('model'=> $model));
	}

	public function actionLookup($postcode)
	{
		$criteria = new CDbCriteria();
		$criteria->compare('postcode', $postcode, true);
		$lookUpResult = Location::model()->findAll($criteria);


 

		if(count($lookUpResult)==0 || empty($postcode)){
			echo 'No address found.';
			return false;
		}

		$this->render('_lookup_form' , array('lookUpResult' => $lookUpResult));
	}

}