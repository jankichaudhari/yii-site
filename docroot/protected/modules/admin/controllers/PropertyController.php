<?php

class PropertyController extends AdminController
{

	public function actionSelect()
	{

		$model = new Property('search');
		$this->render('propertySelect', array('model' => $model));
	}

	public function actionSearch()
	{

		$this->actionSelect();
	}

	public function actionInfo()
	{

		$model = Property::model()->findByPk($_GET['id']);
		$this->render('propertyInfo', ['model' => $model]);
	}

	/**
	 * Method does redirect to itself without ClientId param, it is requred in case we book a valuation.
	 * We have already selected a client and now we selected a property for him,
	 * in case if the client is not current owner of the property we need to suggest
	 * him as new owner and we need to do so once, if people declined to make
	 * the client the new owner we should not annoy them with popup messages anymore.
	 * In order to do so we set flash and redirect to the page without clientId parameter
	 * @param $id
	 * @throws CHttpException
	 */
	public function actionUpdate($id)
	{

		/**
		 * @var $model Property
		 */
		if (isset($_GET['clientId']) && $_GET['clientId']) {
			Yii::app()->user->setFlash('suggest-new-owner', $_GET['clientId']);
			unset($_GET['clientId']);
			$this->redirect(array_merge(['update'], $_GET));
		}

		$model = $this->loadModel($id);
		$this->edit($model);
	}

	public function actionCreate()
	{

		$model = $this->loadModel();
		$this->edit($model);
	}

	/**
	 * if $_GET['owner'] is passed that means that proeprty will have an owner before it is created.
	 * it is used in situation when wi select a client and want to create a property for him (valuation for example) then it is obvious that client is an owner
	 *
	 * @param Property $model property to edit
	 */
	public function edit(Property $model)
	{

		/**
		 * @var $address Address
		 */

		if (isset($_POST['Property']) && $_POST['Property']) {

			$model->attributes = $_POST['Property'];

			$model->setClients(isset($_POST['owner']) && $_POST['owner'] ? $_POST['owner'] : [], Property::CLIENT_TYPE_OWNER);
			$model->setClients(isset($_POST['tenant']) && $_POST['tenant'] ? $_POST['tenant'] : [], Property::CLIENT_TYPE_TENANT);

			if (isset($_POST['propertyAddress']['id']) && $_POST['propertyAddress']['id']) {
				$address = Address::model()->findByPk($_POST['propertyAddress']['id']);
				if ($address) {
					$model->setAddress($address);
				}
			}

			if ($model->save()) {
				Yii::app()->user->setFlash('property-update-success', 'Property updated.');
				$url = array('update', 'id' => $model->pro_id);
				if (isset($_GET['nextStep']) && $_GET['nextStep']) {

					if (isset($_POST['proceed'])) {
						if ($_GET['nextStep'] == 'AppointmentBuilder_propertySelected') ;
						$url = $this->createUrl('AppointmentBuilder/propertySelected', ['propertyId' => $model->pro_id]);
					} else {
						$url ['nextStep'] = $_GET['nextStep'];
					}
				}
				$this->redirect($url);
			}
		}

		$suggesstedOwner = null;
		if (Yii::app()->user->hasFlash('suggest-new-owner')) {
			$suggesstedOwner = Client::model()->findByPk(Yii::app()->user->getFlash('suggest-new-owner', null, false));
		}

		$this->render('edit', ['model' => $model, 'suggestedOwner' => $suggesstedOwner]);
	}

	private function loadModel($id = null)
	{

		if ($id === null) {
			$model = new Property();
		} else {
			$model = Property::model()->findByPk($id);
			if (!$model) {
				throw new CHttpException('404', 'Proeprty [id: ' . $id . '] not found');
			}
		}

		if (isset($_GET['addressId']) && $_GET['addressId']) {

			$address = Address::model()->findByPk($_GET['addressId']);

			if ($address) {
				$model->address   = $address;
				$model->addressId = $address->id;
			}
		}

		if (isset($_GET['owner']) && $_GET['owner']) {
			$model->setClients([$_GET['owner']], Property::CLIENT_TYPE_OWNER);
		}
		if (isset($_GET['tenant']) && $_GET['tenant']) {
			$model->setClients([$_GET['tenant']], Property::CLIENT_TYPE_TENANT);
		}
		return $model;
	}

}
