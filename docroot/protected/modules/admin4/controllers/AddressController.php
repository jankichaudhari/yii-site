<?php

class AddressController extends AdminController
{
	public function actionLookup()
	{

		if ($_GET['postcode']) {
			$api = new PostcodeAPI();
			$api->lookup(array(
							  'postcode' => $_GET['postcode'],
							  'building' => (isset($_GET['building']) ? $_GET['building'] : "")
						 ));
			$result = $api->execute();
			$result = $api->getAsArray();
			$this->renderPartial('lookup', array(
												'items' => $result,
												'error' => $api->getErrors()
										   ));
		}

	}

	public function actionFetch()
	{

		if ($_GET['id']) {
			$api  = new PostcodeAPI();
			$data = $api->fetchData($_GET['id'])->execute()->getAsArray();

			$this->renderPartial('fetch', array(
											   'item'  => $data,
											   'error' => $api->getErrors()
										  ));
		}
	}

	public function actionAutocomplete($search)
	{

		$model               = new Address('search');
		$model->searchString = $search;
		/** @var $data Address[] */
		$data   = $model->search()->getData();
		$result = [];
		foreach ($data as $value) {
			$result[] = [
				'label' => $value->getFullAddressString(' '), 'value' => $value->getFullAddressString(' '),
				'id'    => $value['id']
			];
		}

		echo json_encode($result);

	}

	public function actionLookupNew()
	{

		if ($_GET['postcode']) {
			$api = new PostcodeAPI();
			$api->lookup(array(
							  'postcode' => $_GET['postcode'],
							  'building' => (isset($_GET['building']) ? $_GET['building'] : "")
						 ));
			$result = $api->execute();
			$result = $api->getAsArray();

			$this->renderPartial('lookupNew', array(
												   'items' => $result,
												   'error' => $api->getErrors()
											  ));
		}

	}

	public function actionSelectCoords($id)
	{

		$this->layout = '//layouts/new/popup';
		$this->render('selectCoords', ['objectId' => $id]);
	}

	public function actionSearch()
	{

		$this->layout = '//layouts/new/popup';
		$model        = new Address('search');
		$this->render('search', ['model' => $model]);
	}

	public function actionCreate()
	{

		$model = new Address();
		$this->edit($model);
	}

	public function actionUpdate($id)
	{

		$model = Address::model()->findByPk($id);
		$this->edit($model);
	}

	public function edit(Address $model)
	{

		$this->layout = '//layouts/new/popup';
		$name         = isset($_GET['name']) && $_GET['name'] ? $_GET['name'] : 'Address';
		if (isset($_POST[$name])) {
			$model->attributes = $_POST[$name];

			if ($model->postcodeAnywhereID) {
				/**
				 * checking if we already have an address with same id. if true then lets display that and ask if person want's to use it or save anyway.
				 */
				$criteria = new CDbCriteria();

				$criteria->addCondition('postcodeAnywhereID = "' . $model->postcodeAnywhereID . '"');
				if (!$model->isNewRecord) {
					$criteria->addCondition('id <> "' . $model->id . '"');
				}

				$existingAddresses = Address::model()->findAll($criteria);

				if ($existingAddresses) {
					$this->render('create', [
											'model' => $model, 'existingAddresses' => $existingAddresses,
											'name'  => $name
											]);
					Yii::app()->end();
				}
			}

			if ($model->save()) {
				$params = array('update', 'id' => $model->id, 'name' => $name);
				if (isset($_POST['close']) && $_POST['close']) {
					$params['close'] = true;
				}
				$this->redirect($params);
			}

		}

		$this->render('create', ['model' => $model, 'name' => $name]);
	}

	public function actionInfo($id, $name = 'Address')
	{

		$this->layout = '//layouts/new/popup';
		$model        = Address::model()->findByPk($id);
		$this->renderPartial('newAddressForm', ['fieldName' => $name, 'model' => $model]);
	}

	/**
	 *
	 * returns info for old client_edit screen. crazy compatability.
	 * @param        $id
	 * @param string $name
	 */
	public function actionInfoForOld($id, $name = 'Address')
	{

		$this->layout = '//layouts/new/popup';
		$model        = Address::model()->findByPk($id);
		$this->renderPartial('addressFormForOldEdit', ['fieldName' => $name, 'model' => $model]);
	}

	public function actionMergeTool()
	{

		$this->layout = '//layouts/adminDefault';
		$sql          = "SELECT concat_ws(' ', line1, line2, line3,line4,line5,postcode) address, count(*) `count`, GROUP_CONCAT(id) ids FROM address GROUP BY concat(line1, line2, line3,line4,line5,postcode) HAVING `count` > 1 ORDER BY `count` DESC";
		$totalCount   = "SELECT COUNT(*) FROM (SELECT count(*)`count` FROM address GROUP BY concat(line1, line2, line3,line4,line5,postcode) HAVING `count` > 1) s";
		$count        = Yii::app()->db->createCommand($totalCount)->queryScalar();
		$dataProvider = new CSqlDataProvider($sql, array(
														'totalItemCount' => $count,
														'pagination'     => array("pageSize" => 37),
														'keyField'       => 'ids',
												   ));

		$this->render('mergeTool', ['dataProvider' => $dataProvider]);
	}

	public function actionFetchAndSelect($id)
	{

		$result = ['errorCode' => 0];

		if (!isset($_GET['ignoreExisiting'])) {
			$possibleAddress = Address::model()->findAllByAttributes(['postcodeAnywhereID' => $id]);

			if ($possibleAddress) {
				$result['errorCode'] = 2;
				foreach ($possibleAddress as $address) {
					$result['data'][] = ['id' => $address->id, 'address' => $address->getFullAddressString(' ')];
				}
				echo json_encode($result);
				Yii::app()->end();
			}
		}

		$api  = new PostcodeAPI();
		$data = $api->fetchData($_GET['id'])->execute()->getAsArray();
		if (!$data) {
			$result['errorCode'] = 1;
			$result['errors']    = $api->getErrors();
		} else {
			$data                             = $data[0];
			$address                          = new Address();
			$address->attributes              = $data;
			$address->line1                   = $data['sub_building_name'] ? : $data['name_or_number'];
			$address->line2                   = $data['sub_building_name'] ? trim($data['building_number'] . ' ' . $data['building_name']) : '';
			$address->line3                   = trim($data['thoroughfare_name'] . ' ' . $data['thoroughfare_descriptor']);
			$address->line5                   = $data['post_town'];
			$address->lat                     = $data['latitude'];
			$address->lng                     = $data['longitude'];
			$address->postcodeAnywhereID      = $data['id'];
			$address->postcodeAnywhereGeoData = serialize($data);
			if ($address->save()) {
				$result['data'][] = ['id' => $address->id, 'address' => $address->getFullAddressString(' ')];
			}
		}
		echo json_encode($result);
	}

	public function actionCreateAndSelect()
	{

		$address             = new Address();
		$address->attributes = $_GET;
		$result              = ['errorCode' => 0];
		if ($address->save()) {
			$result['data'][] = ['id' => $address->id, 'address' => $address->getFullAddressString(' ')];
		} else {
			$result['errorCode'] = 1;
			$result['errors']    = $address->getErrors();
		}
		echo json_encode($result);
	}

	public function actionShowOnMap($id)
	{

		$this->layout = '//layouts/new/popup';

		$model = Address::model()->findByPk($id);
		if (!$model) {
			throw new CHttpException(404, 'Address [id=' . $id . '] does not exist');
		}

		if (isset($_POST['Address'])) {
			$model->attributes = $_POST['Address'];
			if ($model->save()) {
				Yii::app()->user->setFlash('address-coordinates-updated', 'Update successfull!');
				$this->redirect(array('showOnMap', 'id' => $id));
			}
		}

		$this->render('showOnMap', array('model' => $model));
	}

	public function actionFix()
	{

		if (isset($_POST['Address']) && $_POST['Address']) {
			$model             = Address::model()->findByPk($_POST['Address']['id']);
			$model->attributes = $_POST['Address'];
			if ($model->save()) {
				$this->redirect('fix');
			}
		}

		$criteria = new CDbCriteria();
		$criteria->addCondition("line3 = ''");
		$criteria->limit = 20;

		$models = Address::model()->findAll($criteria);
		$this->render('fix', ['models' => $models]);

	}

	public function actionEdit($id)
	{

		$model = Address::model()->with('clients', 'properties')->findByPk($id);
		if (!$model) {
			throw new CHttpException(404, 'Address [id = ' . $id . '] is not found');
		}
		if (isset($_POST['Address']) && $_POST['Address']) {

			$model->attributes = $_POST['Address'];
			if ($model->save()) {
				$this->redirect(array('edit', 'id' => $model->id));
			}
		}
		$this->render('edit', ['model' => $model]);
	}
}
